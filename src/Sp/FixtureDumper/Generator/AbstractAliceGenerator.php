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
 * Base class for generating fixtures for the alice library.
 *
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
abstract class AbstractAliceGenerator extends AbstractGenerator
{

    /**
     * {@inheritdoc}
     */
    protected function doGenerate(ClassMetadata $metadata, array $data, array $options = array())
    {
        foreach ($data as $modelName => $values) {
            $data[$modelName] = array_merge($values['fields'], $values['associations']);
        }

        return $this->prepareData($metadata, $data);
    }

    /**
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
     * @param array                                              $data
     *
     * @return mixed
     */
    abstract protected function prepareData(ClassMetadata $metadata, array $data);
}
