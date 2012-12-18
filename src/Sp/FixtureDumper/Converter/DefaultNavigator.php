<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Converter;

use Sp\FixtureDumper\Converter\Handler\HandlerRegistryInterface;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
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

    /**
     * @param Handler\HandlerRegistryInterface $handlerRegistry
     * @param                                  $format
     */
    public function __construct(HandlerRegistryInterface $handlerRegistry, $format)
    {
        $this->handlerRegistry = $handlerRegistry;
        $this->format = $format;
    }

    /**
     * @param VisitorInterface $visitor
     * @param mixed            $value
     * @param null|string      $type
     *
     * @return mixed
     */
    public function accept(VisitorInterface $visitor, $value, $type = null)
    {
        if (null === $type) {
            $type = gettype($value);
            if ('object' ===  $type) {
                $type = get_class($value);
            }
        }

        switch ($type) {
            case 'NULL':
                return $visitor->visitNull($value);
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
            case 'reference':
                return $visitor->visitReference($value);
            default:
                $handler = $this->handlerRegistry->getHandler($type, $this->format);
                if (null !== $handler) {
                    return call_user_func($handler, $visitor, $value);
                }

                return $visitor->visitObject($value);
        }
    }
}
