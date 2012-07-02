<?php
/**
 * Copyright (c) 2011 Elfet
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of
 * the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
 * THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Xa\IoC;

class Container
{
    protected static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * @return \Xa\IoC\Container
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @var \Xa\IoC\Assoc\Assoc[]
     */
    protected $container = array();

    /**
     * Create assoc for interfaces.
     * @param mixed $object
     * @param array $interfaces
     * @throws Exception\UnknownType
     */
    public function register($object, $interfaces = array())
    {
        if (is_string($object)) {
            $assoc = new Assoc\Lazy($object);
        } else if (is_object($object)) {
            $assoc = new Assoc\Reference($object);
        } else {
            throw new Exception\UnknownType('Unknown type of object.');
        }

        $this->assoc($assoc, $interfaces);
    }

    /**
     * Create prototype assoc for interfaces.
     * @param mixed $object
     * @param array $interfaces
     */
    public function prototype($object, $interfaces = array())
    {
        $assoc = new Assoc\Prototype($object);

        $this->assoc($assoc, $interfaces);
    }

    /**
     * Add assoc for interfaces.
     * @param Assoc\Assoc $assoc
     * @param array $interfaces
     */
    public function assoc(Assoc\Assoc $assoc, $interfaces = array())
    {
        if (!is_array($interfaces)) {
            $interfaces = (array)$interfaces;
        }

        if (empty($interfaces)) {
            $interfaces = $assoc->getInterfaces();
        }

        foreach ($interfaces as $interface) {
            $this->container[$interface] = $assoc;
        }
    }

    /**
     * Get assoc object to interface's name.
     * @param string $name
     * @return object
     * @throws Exception\NotAssigned
     */
    public function getObject($name)
    {
        if (isset($this->container[$name])) {
            return $this->container[$name]->getObject();
        } else {
            throw new Exception\NotAssigned("The Association for \"$name\" is not assigned.");
        }
    }

    /**
     * Is have this interface's name association?
     * @param string $name Assoc's interface name.
     * @return bool
     */
    public function haveAssoc($name)
    {
        return isset($this->container[$name]);
    }

    /**
     * @param string $name Assoc's interface name.
     * @return Assoc\Assoc
     */
    public function getAssoc($name)
    {
        return $this->container[$name];
    }

    /**
     * @param string $name Assoc's interface name.
     */
    public function unsetAssoc($name)
    {
        unset($this->container[$name]);
    }

    /**
     * Helper to display container structure.
     */
    public function dump()
    {
        foreach($this->container as $name => $assoc) {
            echo "$name assigned to {$assoc->getClassName()}. \n";
        }
    }
}
