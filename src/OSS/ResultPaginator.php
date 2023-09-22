<?php
namespace OSS;

use OSS\Utils\OssUtil;
use JmesPath\Env as JmesPath;
/**
 * Iterator that yields each page of results of a pageable operation.
 */
class ResultPaginator implements \Iterator
{
    /** @var OssClient Client performing operations. */
    private $client;

    /** @var string Name of the operation being paginated. */
    private $operation;

    /** @var array Args for the operation. */
    private $args;

    /** @var array Configuration for the paginator. */
    private $config;

    /** @var Result Most recent result from the client. */
    private $result;

    /** @var string|array Next token to use for pagination. */
    private $nextToken;

    /** @var int Number of operations/requests performed. */
    private $requestCount = 0;

    /**
     * @param OssClient $client
     * @param string             $operation
     * @param array              $args
     * @param array              $config
     */
    public function __construct(OssClient $client,$operation,array $args,array $config) {
        $this->client = $client;
        $this->operation = $operation;
        $this->args = $args;
        $this->config = $config;
    }

    /**
     * @return Result
     */
    public function current()
    {
        return $this->valid() ? $this->result : false;
    }

    public function key()
    {
        return $this->valid() ? $this->requestCount - 1 : null;
    }

    public function next()
    {
        $this->result = null;
    }

    public function valid()
    {
        if ($this->result) {
            return true;
        }
        if ($this->nextToken || !$this->requestCount) {
            if (isset($this->nextToken)) {
                $this->args = array_replace_recursive($this->args, ['parameters' => $this->nextToken]);
            }
            $result = $this->client->{$this->operation}($this->args);
            $this->result = $result->get('body');
            $this->nextToken = $this->determineNextToken($this->result);
            $this->requestCount++;
            return true;
        }
        return false;
    }

    public function rewind()
    {
        $this->requestCount = 0;
        $this->nextToken = null;
        $this->result = null;
    }

    public function search($expression)
    {
        return OssUtil::flatmap($this, function ($result) use ($expression) {
            return JmesPath::search($expression,$result);
        });
    }

    private function determineNextToken($result)
    {
        if (!$this->config['output_token']) {
            return null;
        }
        if ($this->config['more_results'] && $result[$this->config['more_results']] === 'false') {
            return null;
        }
        $nextToken = is_scalar($this->config['output_token'])
            ? [$this->config['input_token'] => $this->config['output_token']]
            : array_combine($this->config['input_token'], $this->config['output_token']);

        return array_filter(array_map(function ($outputToken) use ($result) {
                return $result[$outputToken];
        }, $nextToken));
    }
}
