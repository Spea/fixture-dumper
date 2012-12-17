<?php

namespace Sp\FixtureDumper\Generator\Alice;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use CG\Generator\Writer;
use Sp\FixtureDumper\Converter\Alice\ArrayVisitor;
use Sp\FixtureDumper\Generator\AliceGenerator;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ArrayFixtureGenerator extends AliceGenerator
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

    protected function getDefaultVisitor()
    {
        return new ArrayVisitor();
    }


}
