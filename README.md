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
    class_exists(\Zend\Expressive\Swoole\ConfigProvider::class)
        ? \Zend\Expressive\Swoole\ConfigProvider::class
        : function () {
            return[];
        },
    \Los\MezzioSwooleNewrelic\ConfigProvider::class,
```

The lib will get newrelic's appname and license from the ini using ini_get, 
but you can define an environment var NEWRELIC_APPNAME to be used 
instead of the newrelic.appname ini setting.
