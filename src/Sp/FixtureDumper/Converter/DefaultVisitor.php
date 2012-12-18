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
class DefaultVisitor implements VisitorInterface
{
    /**
     * {@inheritdoc}
     */
    public function visitString($string)
    {
        return $string;
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
        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function visitReference($reference)
    {
        return $reference;
    }

    /**
     * {@inheritdoc}
     */
    public function visitObject($object)
    {
        return sprintf("unserialize('%s')", serialize($object));
    }

    /**
     * {@inheritdoc}
     */
    public function visitNull($data)
    {
        return null;
    }
}
