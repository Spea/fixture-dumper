<?php

namespace Sp\FixtureDumper\Converter;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class PhpVisitor implements VisitorInterface
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
    public function visitDouble($double)
    {
        return $double;
    }

    /**
     * {@inheritdoc}
     */
    public function visitInteger($integer)
    {
        return $integer;
    }

    /**
     * {@inheritdoc}
     */
    public function visitBoolean($boolean)
    {
        return $boolean ? 'true' : 'false';
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
