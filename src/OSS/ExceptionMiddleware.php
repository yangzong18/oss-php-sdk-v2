<?php

namespace OSS;

use OSS\Exception\ExceptionParser;
use GuzzleHttp\Promise\PromiseInterface;
use OSS\Exception\OssException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ExceptionMiddleware {
    private $nextHandler;
    protected $parser;

    /**
     * @param callable $nextHandler Next handler to invoke.
     */
    public function __construct(callable $nextHandler) {
        $this->nextHandler = $nextHandler;
        $this->parser = new ExceptionParser();
    }

    /**
     * @param RequestInterface $request
     * @return PromiseInterface
     */
    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options) {
        $fn = $this->nextHandler;
        return $fn($request, $options)->then(
            function (ResponseInterface $response) use ($request) {
                return $this->handle($request, $response);
            }
        );
    }

    public function handle(RequestInterface $request, ResponseInterface $response) {
        $code = $response->getStatusCode();
        if ($code < 400) {
            return $response;
        }
        $parts = $this->parser->parse($request, $response);
        throw new OssException($parts);
    }
}
