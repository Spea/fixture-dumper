<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Converter;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class PhpVisitor extends DefaultVisitor
{
    /**
     * {@inheritdoc}
     */
    public function visitString($string)
    {
        return sprintf("'%s'", $string);
    }

    /**
     * {@inheritdoc}
     */
    public function visitArray(array $array)
    {
        return sprintf("unserialize('%s')", serialize($array));
    }

    /**
     * @param array $object
     *
     * @return string
     */
    public function visitObject($object)
    {
        return sprintf("unserialize('%s')", serialize($object));
    }
}
