<?php

namespace Sp\FixtureDumper\Converter\Handler;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class HandlerRegistry implements HandlerRegistryInterface
{
    /**
     * @var array
     */
    protected $handlers;

    /**
     * @param array $handlers
     */
    public function __construct(array $handlers = array())
    {
        $this->handlers = $handlers;
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscribingHandler(HandlerSubscriberInterface $handler)
    {
        $methods = $handler->getSubscribedMethods();

        foreach ($methods as $method) {
            if (!isset($method['format'], $method['type'], $method['method'])) {
                throw new \RuntimeException(sprintf('For each subscribing method a "type", "format" and "method" attribute must be given, but only got "%s" for %s.', implode('" and "', array_keys($method)), get_class($handler)));
            }

            $this->addHandler($method['type'], $method['format'], array($handler, $method['method']));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addHandler($type, $format, $handler)
    {
        if (!is_callable($handler)) {
            throw new \InvalidArgumentException('$handler must be callable');
        }

        $this->handlers[$type][$format] = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function getHandler($type, $format)
    {
        if (!isset($this->handlers[$type][$format])) {
            return null;
        }

        return $this->handlers[$type][$format];
    }
}
