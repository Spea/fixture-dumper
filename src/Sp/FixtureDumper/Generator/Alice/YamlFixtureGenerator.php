<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Generator\Alice;

use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Sp\FixtureDumper\Converter\Alice\YamlVisitor;
use Sp\FixtureDumper\Generator\AbstractAliceGenerator;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class YamlFixtureGenerator extends AbstractAliceGenerator
{

    /**
     * {@inheritdoc}
     */
    public function createFilename(ClassMetadata $metadata, $multipleFiles = true)
    {
        if ($multipleFiles) {
            return lcfirst($this->namingStrategy->fixtureName($metadata) .'.yml');
        }

        return 'fixtures.yml';
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareData(ClassMetadata $metadata, array $data)
    {
        $yaml = new Yaml();

        return $yaml->dump(array($metadata->getName() => $data), 3);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultVisitor()
    {
        return new YamlVisitor();
    }
}
