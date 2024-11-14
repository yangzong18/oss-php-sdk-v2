<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2;

use GuzzleHttp;
use AlibabaCloud\Oss\V2\Exception\ServiceError;

final class Client
{
    /**
     * @var array<string,mixed>
     */
    private $sdkOptions = [
        'product' => 'oss',
        'region' => null,
        'endpoint' => null,
        'retry_max_attempts' => 3,
        'retryer' => null,
        'signer' => null,
        'credentials_provider' => null,
        'address_style' => 'virtual',
        'readwrite_timeout' => null,
        'response_handlers' => null,
        'response_stream' => null,
        'auth_method' => 'header',
        'feature_flags' => 0,
        'additional_headers' => null,
    ];

    private $innerOptions = [
        'handler' => null,
        'user_agent' => null,
    ];

    // guzzle 
    private GuzzleHttp\Client $httpClient;
    private $requestOptions = [];

    public function __construct(Config $config, array $options = [])
    {
        $this->resolveConfig($config);
        $this->resolveOptions($options);
        $this->applyOptions();
    }

    public function invokeOperation(OperationInput $input, array $options = []): OperationOutput
    {
        [$request, $context] = $this->buildRequestContext($input, $options);
        $response = $this->httpClient->send($request, $context);
        return new OperationOutput(
            status: $response->getReasonPhrase(),
            statusCode: $response->getStatusCode(),
            headers: $response->getHeaders(),
            body: $response->getBody(),
            opInput: $input,
            httpResponse: $response,
        );
    }

    private function resolveConfig(Config &$config)
    {
        // client's options
        $options = $this->sdkOptions;
        $options['region'] = $config->getRegion();
        $options['credentials_provider'] = $config->getCredentialsProvider();
        $this->resolveEndpoint($config, $options);
        $this->resolveRetryer($config, $options);
        $this->resolveSigner($config, $options);
        $this->resolveAddressStyle($config, $options);
        $this->resolveFeatureFlags($config, $options);

        $this->sdkOptions = $options;

        // user-agent
        $this->innerOptions['user_agent'] = $this->buildUserAgent($config);

        // guzzle's client
        $options = [];
        $this->resolveHttpClient($config, $options);
        $this->requestOptions = $options;
    }

    private function resolveEndpoint(Config &$config, array &$options)
    {
        $disableSSL = Utils::safetyBool($config->getDisableSSL());
        $endpoint = Utils::safetyString($config->getEndpoint());
        $region = Utils::safetyString($config->getRegion());
        if (\strlen($endpoint) > 0) {
            $endpoint = Utils::addScheme($endpoint, $disableSSL);
        } else if (Validation::isValidRegion($region)) {
        }

        if ($endpoint === '') {
            return;
        }

        $options['endpoint'] = new GuzzleHttp\Psr7\Uri($endpoint);
    }

    private function resolveRetryer(Config &$config, array &$options)
    {
        $options['retryer'] = new Retry\NopRetryer();
    }

    private function resolveSigner(Config &$config, array &$options)
    {
        $options['signer'] = new Signer\SignerV4();
    }

    private function resolveAddressStyle(Config &$config, array &$options)
    {
        $options['address_style'] = 'virtual';
    }

    private function resolveFeatureFlags(Config &$config, array &$options) {}

    private function resolveHttpClient(Config &$config, array &$options)
    {
        // map into GuzzleHttp request options
        if (Utils::safetyBool($config->getEnabledRedirect())) {
            $options[GuzzleHttp\RequestOptions::ALLOW_REDIRECTS] = true;
        }

        if (Utils::safetyBool($config->getInsecureSkipVerify())) {
            $options[GuzzleHttp\RequestOptions::VERIFY] = false;
        }

        $value = $config->getConnectTimeout();
        if (Utils::safetyFloat($value) > 0) {
            $options[GuzzleHttp\RequestOptions::CONNECT_TIMEOUT] = $value;
        }

        $value = $config->getReadwriteTimeout();
        if (Utils::safetyFloat($value) > 0) {
            $options[GuzzleHttp\RequestOptions::READ_TIMEOUT] = $value;
        }

        $value = $config->getProxyHost();
        if (Utils::safetyString($value) !== '') {
            $options[GuzzleHttp\RequestOptions::PROXY] = $value;
        }
    }


    private function resolveOptions(array &$options)
    {
        if (empty($options)) {
            return;
        }

        // sdk options
        $opt = \array_filter($options, function ($key) {
            return \array_key_exists($key, $this->sdkOptions);
        }, \ARRAY_FILTER_USE_KEY);
        $this->sdkOptions = array_replace($this->sdkOptions, $opt);

        // inner options
        $opt = \array_filter($options, function ($key) {
            return \array_key_exists($key, $this->innerOptions);
        }, \ARRAY_FILTER_USE_KEY);
        $this->innerOptions = array_replace($this->innerOptions, $opt);

        // Guzzle's request options
        $src = $options['request_options'];
        if (!empty($src)) {
            $allows = [
                'allow_redirects',
                'proxy',
                'expect',
                'cert',
                'verify',
                'ssl_key',
                'connect_timeout',
                'read_timeout',
                'debug',
                'decode_content',
            ];
            $opt = \array_filter($src, function ($key) use ($allows) {
                return in_array($key, $allows);
            }, \ARRAY_FILTER_USE_KEY);
            $this->requestOptions = array_replace($this->requestOptions, $opt);
        }
    }

    private function applyOptions()
    {
        // GuzzleHttp\Client
        // request options
        $config = [
            GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => false,
            GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => 10.0,
            GuzzleHttp\RequestOptions::READ_TIMEOUT => 20.0,
            GuzzleHttp\RequestOptions::VERIFY => true,
        ];
        $config = \array_merge($config, $this->requestOptions);

        // stack
        $handler = $this->innerOptions['handler'] ?: GuzzleHttp\Utils::chooseHandler();
        $stack = new GuzzleHttp\HandlerStack($handler);

        // retryer
        $stack->push(RetryMiddleware::create(
            static function (
                int $retries,
                \Psr\Http\Message\RequestInterface $request,
                \Throwable $reason,
                array $options
            ) {

                if (!$request->getBody()->isSeekable()) {
                    return false;
                }

                if ($retries + 1 >= $options['sdk_context']['retry_max_attempts']) {
                    return false;
                }

                // api's timeout


                // retryable error
                if (!$options['sdk_context']['retryer']->isErrorRetryable($reason)) {
                    return false;
                }

                // reset state
                $request->getBody()->rewind();
                if ($options['sdk_context']['reset_time']) {
                    $options['signing_context']->time = null;
                }

                //printf("retry cnt %d, %d\n", $retries, $options['sdk_context']['retry_max_attempts']);
                return true;
            },
            static function (int $retries, array $options): int {
                //int in milliseconds
                $delay = $options['sdk_context']['retryer']->retryDelay($retries, null);
                return (int)($delay * 1000);
            },
        ), 'retryer');

        // signer
        $stack->push(static function (callable $handler): callable {
            return static function (\Psr\Http\Message\RequestInterface $request, array $options) use ($handler) {
                #var_dump($options);
                $sdk_context = $options['sdk_context'];
                $provider = $sdk_context['credentials_provider'];
                if (!($provider instanceof Credentials\AnonymousCredentialsProvider)) {
                    try {
                        $cred = $provider->getCredentials();
                    } catch (\Exception $e) {
                        throw new Exception\CredentialsFetchError($e);
                    }

                    if (!$cred->hasKeys()) {
                        throw new Exception\CredentialsEmptyError();
                    }
                    $signer = $sdk_context['signer'];
                    $signingContext = $options['signing_context'];
                    $signingContext->request = $request;
                    $signingContext->credentials = $cred;
                    $signer->sign($signingContext);
                    $request = $signingContext->request;
                }
                return $handler($request, $options);
            };
        }, 'signer');

        // http response checker
        $stack->push(static function (callable $handler): callable {
            return static function ($request, array $options) use ($handler) {
                if (empty($options['response_handlers'])) {
                    return $handler($request, $options);
                }
                return $handler($request, $options)->then(
                    static function (\Psr\Http\Message\ResponseInterface $response) use ($request, $options) {
                        foreach ($options['response_handlers'] as $h) {
                            if (\is_callable($h)) {
                                $h($request, $response, $options);
                            } else {
                                call_user_func($h, $request, $response, $options);
                            }
                        }
                        return $response;
                    }
                );
            };
        }, 'response_handlers');

        $stack->push(GuzzleHttp\Middleware::redirect(), 'allow_redirects');
        $stack->push(GuzzleHttp\Middleware::prepareBody(), 'prepare_body');

        $config['handler'] = $stack;

        $this->httpClient = new GuzzleHttp\Client($config);
    }


    private function buildUserAgent(Config &$config)
    {
        return Utils::defaultUserAgent();
    }

    private function buildUri(OperationInput &$input): \Psr\Http\Message\UriInterface
    {
        $paths = [];
        $uri = new GuzzleHttp\Psr7\Uri(
            $this->sdkOptions['endpoint']->getScheme() . "://" . $this->sdkOptions['endpoint']->getAuthority()
        );

        if ($input->getBucket() != null) {
            switch ($this->sdkOptions['address_style']) {
                case 'path':
                    array_push($paths, $input->getBucket());
                    if ($input->getKey() == null) {
                        array_push($paths, '');
                    }
                    break;
                case 'cname':
                    break;
                default:
                    $uri = $uri->withHost($input->getBucket() . '.' . $uri->getHost());
                    break;
            }
        }

        if ($input->getKey() != null) {
            array_push($paths, Utils::urlEncode($input->getKey(), true));
        }

        return $uri->withPath('/' . implode('/', $paths));
    }

    private function buildRequestContext(OperationInput &$input, array &$options)
    {
        $context = [];
        // GuzzleHttp's request options for api
        if (isset($options['connect_timeout'])) {
            $context['connect_timeout'] =  $options['connect_timeout'];
        }
        if (isset($options['read_timeout'])) {
            $context['read_timeout'] =  $options['read_timeout'];
        }
        if (isset($options['timeout'])) {
            $context['timeout'] =  $options['timeout'];
        }

        // sdk's part
        $retry_max_attempts = $options['retry_max_attempts'] ?? $this->sdkOptions['retry_max_attempts'];
        $sdk_context = [
            'retry_max_attempts' => \max($retry_max_attempts, 1),
            'retryer' => $options['retryer'] ?? $this->sdkOptions['retryer'],
            'signer' => $this->sdkOptions['signer'],
            'credentials_provider' => $this->sdkOptions['credentials_provider'],
        ];
        $context['sdk_context'] = $sdk_context;


        // Requst
        // host & path & query
        $uri = $this->buildUri($input);
        $query = $input->getParameters();
        if (!empty($query)) {
            $uri = $uri->withQuery(
                http_build_query($query, '', '&', PHP_QUERY_RFC3986)
            );
        }

        $request = new GuzzleHttp\Psr7\Request(
            $input->getMethod(),
            $uri,
            $input->getHeaders(),
            $input->getBody(),
        );

        $request = $request->withAddedHeader('User-Agent', $this->innerOptions['user_agent']);

        // signing context
        $signingContext = new Signer\SigningContext(
            product: $this->sdkOptions['product'],
            region: $this->sdkOptions['region'],
            bucket: $input->getBucket(),
            key: $input->getKey(),
            request: $request,
            authMethodQuery: $this->sdkOptions['auth_method'] === 'query',
            subResource: ($input->getOpMetadata())['sub-resource'] ?? [],
        );

        // signing time from user

        $context['signing_context'] = $signingContext;

        $context['sdk_context']['reset_time'] = $signingContext->time == null;

        // response-handler
        $responseHandlers = [
            [Client::class, 'httpErrors'],
        ];

        $context['response_handlers'] = $responseHandlers;

        return [$request, $context];
    }

    private static function httpErrors(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $options
    ) {
        $statusCode = $response->getStatusCode();
        if (intval($statusCode / 100) == 2) {
            return;
        }
        $content = $response->getBody()->getContents();
        $code = 'BadErrorResponse';
        $message = '';
        $ec = '';
        $requestId = '';
        $errorFileds = [];

        $xmlStr = $content;
        if ($xmlStr === '' && $response->hasHeader('x-oss-err')) {
            $xmlStr = base64_decode($response->getHeader('x-oss-err')[0]);
        }

        if (str_contains($xmlStr, '<Error>')) {
            $xml = simplexml_load_string($xmlStr);
            if (false === $xml) {
                $message = 'Failed to parse xml from response body, part response body ' . substr($xmlStr, 0, 256);
            }
            $code = $xml->Message ?? $code;
            $message = $xml->Message ?? '';
            $ec = $xml->EC ?? '';
            $requestId = $xml->RequestId ?? '';
            foreach ($xml as $key => $val) {
                $errorFileds[$key] = (string)$val;
            }
        } else {
            $message = 'Not found tag <Error>, part response body ' . substr($xmlStr, 0, 256);;
        }

        if ($requestId == '' && $response->hasHeader('x-oss-request-id')) {
            $requestId = $response->getHeader('x-oss-request-id')[0];
        }

        if ($ec == '' && $response->hasHeader('x-oss-ec')) {
            $ec = $response->getHeader('x-oss-ec')[0];
        }

        throw new ServiceError(
            [
                'status_code' => $statusCode,
                'request_id' => $requestId,
                'code' => $code,
                'message' => $message,
                'ec' => $ec,
                'request_target' => $request->getMethod() . ' ' . $request->getUri(),
                'snapshot' => $content,
                'headers' => $response->getHeaders(),
                'error_fileds' => $errorFileds
            ]
        );
    }
}
