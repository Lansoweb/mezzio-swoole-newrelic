# Mezzio Swoole Newrelic

This lib enables Newrelic transactions and errors when using PHP with Swoole.

### Installation

```
composer require los/mezzio-swoole-newrelic
```

### Configuration

This lib provides a ConfigProvider.php and should be injected inside your config.php. 
Just be sure that it's after the default Swoole module:

```php
    // Swoole config to overwrite some services (if installed)
    class_exists(\Mezzio\Swoole\ConfigProvider::class)
        ? \Mezzio\Swoole\ConfigProvider::class
        : function () {
            return[];
        },
    \Los\MezzioSwooleNewrelic\ConfigProvider::class,
```

The lib will get newrelic's appname and license from the ini using ini_get, 
but you can define an environment var NEWRELIC_APPNAME to be used 
instead of the newrelic.appname ini setting.

There is also a NewRelicMiddleware that will name the transactions with the zend-expressive-router router names. Just include inside your pipeline.php after the RouterMiddleware:
```php
$app->pipe(RouteMiddleware::class);
$app->pipe(NewRelicMiddleware::class);
```
