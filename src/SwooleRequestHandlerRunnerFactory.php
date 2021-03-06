<?php

declare(strict_types=1);

namespace Los\MezzioSwooleNewrelic;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Server as SwooleHttpServer;
use Mezzio\ApplicationPipeline;
use Mezzio\Response\ServerRequestErrorResponseGenerator;
use Mezzio\Swoole\HotCodeReload\Reloader;
use Mezzio\Swoole\Log\AccessLogInterface;
use Mezzio\Swoole\PidManager;
use Mezzio\Swoole\StaticResourceHandlerInterface;

class SwooleRequestHandlerRunnerFactory
{
    public function __invoke(ContainerInterface $container) : SwooleRequestHandlerRunner
    {
        $logger = $container->has(AccessLogInterface::class)
            ? $container->get(AccessLogInterface::class)
            : null;

        $expressiveSwooleConfig = $container->has('config')
            ? $container->get('config')['mezzio-swoole']
            : [];

        $swooleHttpServerConfig = $expressiveSwooleConfig['swoole-http-server'] ?? [];

        return new SwooleRequestHandlerRunner(
            $container->get(ApplicationPipeline::class),
            $container->get(ServerRequestInterface::class),
            $container->get(ServerRequestErrorResponseGenerator::class),
            $container->get(PidManager::class),
            $container->get(SwooleHttpServer::class),
            $this->retrieveStaticResourceHandler($container, $swooleHttpServerConfig),
            $logger,
            $swooleHttpServerConfig['process-name'] ?? SwooleRequestHandlerRunner::DEFAULT_PROCESS_NAME,
            $this->retrieveHotCodeReloader($container, $expressiveSwooleConfig)
        );
    }

    private function retrieveStaticResourceHandler(
        ContainerInterface $container,
        array $config
    ) : ?StaticResourceHandlerInterface {
        $config = $config['static-files'] ?? [];
        $enabled = isset($config['enable']) && true === $config['enable'];

        return $enabled && $container->has(StaticResourceHandlerInterface::class)
            ? $container->get(StaticResourceHandlerInterface::class)
            : null;
    }

    private function retrieveHotCodeReloader(
        ContainerInterface $container,
        array $config
    ) : ?Reloader {
        $config = $config['hot-code-reload'] ?? [];
        $enabled = isset($config['enable']) && true === $config['enable'];

        return $enabled && $container->has(Reloader::class)
            ? $container->get(Reloader::class)
            : null;
    }
}
