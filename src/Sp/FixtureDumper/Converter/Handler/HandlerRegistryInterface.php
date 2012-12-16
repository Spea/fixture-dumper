<?php

namespace Sp\FixtureDumper\Converter\Handler;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
interface HandlerRegistryInterface
{
    /**
     * @param ConverterSubscriberInterface $handler
     *
     * @return void
     */
    public function addSubscribingHandler(HandlerSubscriberInterface $handler);

    /**
     * Registers a handler in the registry.
     *
     * @param string $type
     * @param string $format
     * @param callable $handler function(VisitorInterface, mixed $data, array $type): mixed
     *
     * @return void
     */
    public function addHandler($type, $format, $handler);

    /**
     * @param string $type
     * @param string $format
     *
     * @return callable|null
     */
    public function getHandler($type, $format);

}
