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
