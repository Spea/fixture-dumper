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

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use CG\Generator\Writer;
use Sp\FixtureDumper\Converter\Alice\ArrayVisitor;
use Sp\FixtureDumper\Generator\AbstractAliceGenerator;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ArrayFixtureGenerator extends AbstractAliceGenerator
{
    /**
     * @var \CG\Generator\Writer
     */
    protected $writer;

    /**
     * {@inheritdoc}
     */
    public function createFilename(ClassMetadata $metadata, $multipleFiles = true)
    {
        if ($multipleFiles) {
            return lcfirst($this->namingStrategy->fixtureName($metadata) .'.php');
        }

        return 'fixtures.php';
    }

    /**
     * {@inheritdoc}
     */
    public function prepareForWrite($fixture)
    {
        $writer = new Writer();
        $writer->writeln("<?php\n");

        $writer
            ->writeln('/**')
            ->writeln(' * This code was generated automatically by the FixtureDumper library, manual changes to it')
            ->writeln(' * may be lost upon next generation.')
            ->writeln(' */');

        $writer->writeln("\$fixtures = array();");
        $writer->writeln($fixture. "\n");
        $writer->writeln("return \$fixtures;");

        return $writer->getContent();
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareData(ClassMetadata $metadata, array $data)
    {
        $writer = new Writer();
        $writer->writeln(sprintf('$fixtures[\'%s\'] = array(', $metadata->getName()));

        foreach ($data as $key => $values) {
            $writer
                ->indent()
                ->writeln(sprintf("'%s' => array(", $key))
                ->indent();

            $arrayValues = array();
            foreach ($values as $k => $value) {
                $arrayValues[] = sprintf("'%s' => %s", $k, $this->convertValue($value));
            }

            $writer
                ->writeln(implode(",\n", $arrayValues))
                ->outdent()
                ->writeln("),")
                ->outdent();
        }

        $writer->writeln(');');

        return $writer->getContent();
    }

    /**
     * Converts a value to its php representation.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function convertValue($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        $result = 'array(';
        $values = array();
        foreach ($value as $key => $val) {
            $values[] .= sprintf("'%s' => %s", $key, $this->convertValue($val));
        }

        $result .= implode(",", $values);
        $result .= ')';

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultVisitor()
    {
        return new ArrayVisitor();
    }
}
