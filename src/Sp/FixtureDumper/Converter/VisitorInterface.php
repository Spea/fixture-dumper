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
interface VisitorInterface
{
    /**
     * @param string $string
     *
     * @return string
     */
    public function visitString($string);

    /**
     * @param float $double
     *
     * @return string
     */
    public function visitDouble($double);

    /**
     * @param integer $integer
     *
     * @return string
     */
    public function visitInteger($integer);

    /**
     * @param boolean $boolean
     *
     * @return string
     */
    public function visitBoolean($boolean);

    /**
     * @param array $array
     *
     * @return string
     */
    public function visitArray(array $array);

    /**
     * @param mixed $reference
     *
     * @return string
     */
    public function visitReference($reference);

    /**
     * @param array $object
     *
     * @return string
     */
    public function visitObject($object);
}
