<?php

namespace Sp\FixtureDumper\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ClassNamingStrategy extends DefaultNamingStrategy
{
    public function fixtureName(ClassMetadata $metadata)
    {
        return 'Load'. parent::fixtureName($metadata) .'Data';
    }

}
