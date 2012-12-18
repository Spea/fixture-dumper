<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Sp\FixtureDumper\Util\ClassUtils;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class DefaultNamingStrategy implements NamingStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function fixtureName(ClassMetadata $metadata)
    {
        return ClassUtils::getClassName($metadata->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function modelName($model, ClassMetadata $metadata)
    {
        $identifiers = $metadata->getIdentifierValues($model);
        $className = lcfirst(ClassUtils::getClassName($metadata->getName()));

        return $className . implode('_', $identifiers);
    }

}
