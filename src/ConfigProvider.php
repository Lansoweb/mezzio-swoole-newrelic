<?php

declare(strict_types=1);

namespace Los\MezzioSwooleNewrelic;

use Mezzio\Swoole\SwooleRequestHandlerRunner;
use Laminas\Stratigility\Middleware\ErrorHandler;

/**
 * The configuration provider for the Api Hotel module
 *
 * @codeCoverageIgnore
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'factories'  => [
                SwooleRequestHandlerRunner::class => SwooleRequestHandlerRunnerFactory::class,
            ],
            'delegators' => [
                ErrorHandler::class => [
                    ErrorHandlerListenerDelegatorFactory::class,
                ],
            ],
        ];
    }
}
