<?php
namespace OSS;

use OSS\Exception\OssException;
use OSS\Signer\Signer;
use OSS\Utils\OssUtil;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResultTransformer {

    /**
     * @var OperationInput
     */
    private $input;

    /**
     * @var mixed|string|null
     */
    private $action;

    /**
     * @var Result
     */
    private $result;

    public function __construct(OperationInput $input,Result $result) {
        $this->input = $input;
        $this->result = $result;
        $this->action = $input->getOperationName();
    }
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param Result $result
     * @return Result
     */
    public function headerTransformer(RequestInterface $request, ResponseInterface $response, Result $result)
    {
        $headers = $response->getHeaders();
        $metadata = array();
        $responseHeaders = array();
        foreach ($headers as $key => $value) {
            if (strpos($key, "x-oss-meta-") === 0) {
                $metadata[substr($key, 11)] = $value[0];
            } else {
                $responseHeaders[$key] = $value[0];
            }
        }
        if (!empty($metadata)) {
            $result['metaData'] = $metadata;
        }
        if (!empty($responseHeaders)) {
            $result['headers'] = $responseHeaders;
        }

        $headers = $request->getHeaders();
        $requestHeaders = [];
        foreach ($headers as $key => $value) {
            $requestHeaders[$key] = $value[0];
        }
        if (!empty($requestHeaders)) {
            $result['requestHeaders'] = $requestHeaders;
        }
        $result['requestUrl'] = strval($request->getUri());
        return $result;
    }


    /**
     * @param ResponseInterface $response
     * @param Result $result
     * @return Result
     */
    public function bodyTransformer(ResponseInterface $response, Result $result)
    {
        $contentType = $response->getHeaderLine(Signer::CONTENT_TYPE_HEADER);
        if ($response->getBody()->getSize() > 0){
            $body = $response->getBody();
            switch ($contentType){
                case 'application/xml':
                    $result['body'] = $this->parseBody($body->getContents());
                    break;
                case 'application/json':
                    $result['body'] = json_decode($body->getContents(),true);
                    break;
                default:
                    $result['body'] = $body;
            }
        }
        return $result;
    }


    /**
     * @param string $body
     * @return mixed
     */
    protected function parseBody($body){
        $keys = OssUtil::getInterfaceKey($this->action);
        $bodyArray = json_decode(json_encode(simplexml_load_string($body,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        if (!$bodyArray){
            throw new OssException('Simplexml load string parsing XML error');
        }
        if (isset($keys) && count($keys) > 0){
            foreach ($keys as $key){
                OssUtil::replaceBodyArray($bodyArray,$key);
            }
        }
        return $bodyArray;
    }
    public function __destruct(){}

}
