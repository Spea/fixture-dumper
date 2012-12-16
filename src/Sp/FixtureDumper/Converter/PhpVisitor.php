<?php

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
