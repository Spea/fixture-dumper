<?php

/*
 * This file is part of the FixtureDumper library.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumper\Converter\Handler;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
interface HandlerRegistryInterface
{
    /**
     * Add a subscribing handler to the registry.
     *
     * @param HandlerSubscriberInterface $handler
     *
     * @return void
     */
    public function addSubscribingHandler(HandlerSubscriberInterface $handler);

    /**
     * Adds a handler in the registry.
     *
     * @param string $type
     * @param string $format
     * @param callable $handler function(VisitorInterface, mixed $data, array $type): mixed
     *
     * @return void
     */
    public function addHandler($type, $format, $handler);

    /**
     * Returns the the handler for the given type and format.
     *
     * @param string $type
     * @param string $format
     *
     * @return callable|null
     */
    public function getHandler($type, $format);

}
