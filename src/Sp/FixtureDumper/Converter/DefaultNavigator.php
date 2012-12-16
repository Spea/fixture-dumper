<?php

namespace Sp\FixtureDumper\Converter;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
use Sp\FixtureDumper\Converter\Handler\HandlerRegistryInterface;

class DefaultNavigator
{
    /**
     * @var Handler\HandlerRegistryInterface
     */
    protected $handlerRegistry;

    /**
     * @var string
     */
    protected $format;

    public function __construct(HandlerRegistryInterface $handlerRegistry, $format)
    {
        $this->handlerRegistry = $handlerRegistry;
        $this->format = $format;
    }

    /**
     * @param VisitorInterface $visitor
     * @param  mixed           $value
     *
     * @return string
     */
    public function accept(VisitorInterface $visitor, $value)
    {
        $type = gettype($value);

        if ('object' ===  $type) {
            $type = get_class($value);
        }

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
                $handler = $this->handlerRegistry->getHandler($type, $this->format);
                if (null !== $handler) {
                    return call_user_func($handler, $visitor, $value);
                }

                return $visitor->visitObject($value);
        }
    }
}
