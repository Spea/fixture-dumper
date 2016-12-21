<?php

namespace Sp\FixtureDumper\ExclusionStrategy;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Sp\FixtureDumper\Util\ClassUtils;

/**
 * @author Miguel González <infinit89@gmail.com>
 */
class ArrayExclusionStrategy implements ExclusionStrategyInterface
{
    /** @var array */
    private $skipClassesNames;

    public function __construct(array $skipClassesNames)
    {
        $this->skipClassesNames = $skipClassesNames;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata)
    {
        return in_array(ClassUtils::getClassName($metadata->getName()), $this->skipClassesNames) ||
               in_array($metadata->getName(), $this->skipClassesNames);
    }
}
