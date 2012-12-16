<?php

namespace Sp\FixtureDumper\Generator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
abstract class NelmioGenerator extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    protected function doGenerate(ClassMetadata $metadata, array $data = null, array $options = array())
    {
        foreach ($data as $modelName => $values) {
            $data[$modelName] = array_merge($values['fields'], $values['associations']);
        }

        return $this->prepareData($metadata, $data);
    }

    abstract protected function prepareData(ClassMetadata $metadata, array $data);
}
