# Phystrix Bundle

This bundle provides a phystrix command factory service: `phystrix.command_factory` with default configuration

## Installation

Install component by using [Composer](https://getcomposer.org).
Update your project's `composer.json` file to include dependency.

```json
"require": {
    "odesk/phystrix-bundle": "~1.1"
}
```

Note that code is stored in our composer repository generator [satis](http://satis.odesk.com).
If you haven't included "satis" into your `composer.json`, include it to the `repositories` section too.

```json
"repositories": [
    { "type": "composer", "url": "http://satis.odesk.com/" }
]
```


Register bundle in your `AppKernel`

``` php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Odesk\Bundle\PhystrixBundle\OdeskPhystrixBundle()
            // ...
        );
    }
}
```

## Configuration

Default configuration:

### app/config/config.yml

```yaml
odesk_phystrix:
  default:
    fallback: ~
    circuitBreaker:
      errorThresholdPercentage: 50
      forceOpen: false
      forceClosed: false
      requestVolumeThreshold: 20
      sleepWindowInMilliseconds: 5000
    metrics:
      healthSnapshotIntervalInMilliseconds: 1000
      rollingStatisticalWindowInMilliseconds: 10000
      rollingStatisticalWindowBuckets: 10
    requestCache: ~
    requestLog: ~
```

## Web Profiler

Phystrix bundles comes with a web profiler plugin, it is enabled automatically as long as the profiler enabled.
You only need to make sure requestLog feature is turned on:

```yaml
odesk_phystrix:
  default:
    requestLog:
      enabled: true
```

Only do this in mode/environment where profiler is active.

## Usage

You may use `phystrix.service_locator` to provide additional dependencies in runtime:

```php
$container->get('phystrix.service_locator')->set('somekey', $somevalue);
```

How to create and run a command:

```php
$command = $container->get('phystrix.command_factory')->getCommand('MyCommand', $parameter1, $parameter2);
$command->execute();
```

