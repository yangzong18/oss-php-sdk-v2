<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Types;

use Exception;

class Model
{
    public function __call($method, $args) 
    {
        #echo "Calling method '$method', args: ". implode(', ', $args). "\n";
        // setter
        if(strncmp($method, "set", 3) === 0)
        {
            $rc = new \ReflectionObject($this);
            $name = lcfirst(string: substr($method, 3));
            $prop = $rc->getProperty($name);
            $prop->setValue($this, $args[0]);
            return $this;
        }
        // getter 
        else if (strncmp($method, "get", 3) === 0) 
        {
            $rc = new \ReflectionObject($this);
            $name = lcfirst(string: substr($method, 3));
            $prop = $rc->getProperty($name);
            return $prop->getValue($this);
        } else {
            throw new Exception($method . "function does not exist");
        }
    }
}
