<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Util;

use InvalidArgumentException;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
final class ClassUtils
{
    private function __construct()
    {}

    /**
     * Strip the namespace from a given class or object and get only the class name.
     *
     * @param mixed $class
     *
     * @throws \InvalidArgumentException
     * @return bool|string
     */
    public static function getClassName($class)
    {
        if (!is_object($class) && !is_string($class)) {
            throw new InvalidArgumentException(sprintf('$object must be a string or an object, %s given', gettype($class)));
        }

        $className = is_string($class) ? $class : get_class($class);
        $pos = strrpos($className, '\\');

        if ($pos !== false) {
            $className = substr($className, ($pos + 1));
        }

        return $className;
    }

    /**
     * Get the namespace from a given class or object.
     *
     * @param mixed $class
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getNamespace($class)
    {
        if (!is_object($class) && !is_string($class)) {
            throw new InvalidArgumentException('$object must be a string or an object');
        }

        $class = (is_string($class) ? $class : get_class($class));
        $pos = strrpos($class, '\\');

        return substr($class, 0, $pos);
    }
}
