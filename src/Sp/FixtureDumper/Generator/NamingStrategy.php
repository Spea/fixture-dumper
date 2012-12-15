<?php

namespace Sp\FixtureDumper\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
interface NamingStrategy
{
    /**
     * Return a fixture class name for the given model class name.
     *
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
     *
     * @return string A class name for the fixture
     */
    function fixtureName(ClassMetadata $metadata);

    /**
     * Returns a name for the given model
     *
     * @param mixed                                              $model
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
     *
     * @return string
     */
    function modelName($model, ClassMetadata $metadata);
}
