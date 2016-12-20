<?php

namespace Sp\FixtureDumper\ExclusionStrategy;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * Interface ExclusionStrategyInterface
 *
 * @package Sp\FixtureDumper\ExclusionStrategy
 * @author Miguel González <infinit89@gmail.com>
 */
interface ExclusionStrategyInterface
{

    /**
     * Indicates if this class should be skipped and do not generate fixtures for this class
     *
     * @param $metadata
     *
     * @return boolean true, if this class should be skipped
     */
    public function shouldSkipClass(ClassMetadata $metadata);
}