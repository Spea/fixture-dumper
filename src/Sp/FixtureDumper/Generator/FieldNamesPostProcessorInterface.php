<?php
/**
 * Created by PhpStorm.
 * User: andreasschacht
 * Date: 01.08.16
 * Time: 11:27
 */

namespace Sp\FixtureDumper\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * Interface FieldNamesPostProcessorInterface
 *
 * In order to postprocess generates fields array, implement this interface
 * and add it to the generator
 *
 * @package Sp\FixtureDumper\Generator
 */
interface FieldNamesPostProcessorInterface
{

    /**
     * Indicates if this class supports processing data
     *
     * @param               $object
     * @param ClassMetaData $metadata
     * @param array         $data
     *
     *
     * @return boolean true, if this class can process the data
     */
    public function isSupporting($object, ClassMetaData $metadata, array $data);

    /**
     * returns the processed data
     *
     * @param               $object
     * @param ClassMetaData $metadata
     * @param array         $data
     *
     * @return array
     */
    public function getData($object, ClassMetaData $metadata, array $data);
}