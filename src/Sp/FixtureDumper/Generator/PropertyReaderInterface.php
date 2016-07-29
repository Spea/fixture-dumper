<?php

namespace Sp\FixtureDumper\Generator;

/**
 * Interface PropertyReaderInterface
 *
 * @package Sp\FixtureDumper\Generator
 * @author Andreas Schacht
 *
 */
interface PropertyReaderInterface
{
    /**
     * Indicates if this class supports reading a value from the property
     *
     * @param $object
     * @param $property
     *
     * @return boolean true, if this class can read a value from the property
     */
    public function isSupporting($object, $property);

    /**
     * returns the value from the property
     *
     * @param $object
     * @param $property
     *
     * @return mixed
     */
    public function getValue($object, $property);
}