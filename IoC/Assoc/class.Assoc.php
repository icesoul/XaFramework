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

namespace Xa\IoC\Assoc;

abstract class Assoc
{
    protected $className;

    /**
     * @param string $className The class name of the association
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @abstract
     * @return object
     */
    public abstract function getObject();

    /**
     * Return interfaces for assoc class.
     * @return array
     */
    public function getInterfaces()
    {
        $reflectionClass = new \ReflectionClass($this->className);
        $reflectionInterfaces = $reflectionClass->getInterfaces();

        $interfaces = array();

        foreach($reflectionInterfaces as $reflectionClassInterface) {
            $interfaces[] = $reflectionClassInterface->getName();
        }

        return $interfaces;
    }

    /**
     * Return class name of the association.
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
