<?php
namespace OSS;
use JmesPath\Env as JmesPath;
/**
 * Class Result
 * @package OSS
 */
class Result implements ResultInterface
{
    use HasDataTrait;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function hasKey($name)
    {
        return isset($this->data[$name]);
    }

    public function get($key)
    {
        return $this[$key];
    }

    public function __toString()
    {
        $jsonData = json_encode($this->toArray(), JSON_PRETTY_PRINT);
        return <<<EOT
{$jsonData}
EOT;
    }
}
