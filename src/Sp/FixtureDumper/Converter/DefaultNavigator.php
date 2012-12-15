<?php

namespace Sp\FixtureDumper\Converter;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class DefaultNavigator
{

    protected $handlerRegistry;

    public function accept(VisitorInterface $visitor, $value)
    {
        $type = gettype($value);

        switch ($type) {
            case 'string':
                return $visitor->visitString($value);
            case 'integer':
                return $visitor->visitInteger($value);
            case 'float':
            case 'double':
                return $visitor->visitDouble($value);
            case 'boolean':
                return $visitor->visitBoolean($value);
            case 'array':
                return $visitor->visitArray($value);
            default:
                return $visitor->visitObject($value);
        }
    }
}
