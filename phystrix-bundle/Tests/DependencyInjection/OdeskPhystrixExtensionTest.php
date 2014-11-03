<?php

namespace Odesk\Bundle\PhystrixBundle\Tests\DependencyInjection;

use Odesk\Bundle\PhystrixBundle\DependencyInjection\OdeskPhystrixExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OdeskPhystrixExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OdeskPhystrixExtension
     */
    private $extension;

    protected function setUp()
    {
        parent::setUp();
        $this->extension = new OdeskPhystrixExtension();
    }

    public function publicServicesNamesProvider()
    {
        return array(
            array('phystrix.command_factory'),
            array('phystrix.service_locator'),
        );
    }

    /**
     * @param string $serviceName
     * @dataProvider publicServicesNamesProvider
     */
    public function testServiceIsPublic($serviceName)
    {
        $container = new ContainerBuilder;
        $this->extension->load(array(array('default' => array())), $container);

        $this->assertTrue($container->hasDefinition($serviceName), "Service $serviceName must be defined");
        $definition = $container->getDefinition($serviceName);
        $this->assertTrue($definition->isPublic(), "Service $serviceName must be public");
    }

    public function testDefaultConfig()
    {
        $container = new ContainerBuilder();
        $this->extension->load(array(array('default' => array())), $container);

        $configArrayAll = $container->getParameter('phystrix.configuration.data');

        $this->assertArrayHasKey('default', $configArrayAll);

        $defaultConfigArray = $configArrayAll['default'];

        // fallback
        $this->assertArrayHasKey('fallback', $defaultConfigArray);
        $this->assertEquals(false, $defaultConfigArray['fallback']['enabled']);

        // requestCache
        $this->assertArrayHasKey('requestCache', $defaultConfigArray);
        $this->assertEquals(true, $defaultConfigArray['requestCache']['enabled']);

        // requestLog
        $this->assertArrayHasKey('requestLog', $defaultConfigArray);
        $this->assertEquals(false, $defaultConfigArray['fallback']['enabled']);

        // circuitBreaker
        $this->assertArrayHasKey('circuitBreaker', $defaultConfigArray);
        $circuitBreakerConfigArray = $defaultConfigArray['circuitBreaker'];

        $this->assertArrayHasKey('errorThresholdPercentage', $circuitBreakerConfigArray);
        $this->assertEquals(50, $circuitBreakerConfigArray['errorThresholdPercentage']);

        $this->assertArrayHasKey('requestVolumeThreshold', $circuitBreakerConfigArray);
        $this->assertEquals(20, $circuitBreakerConfigArray['requestVolumeThreshold']);

        $this->assertArrayHasKey('sleepWindowInMilliseconds', $circuitBreakerConfigArray);
        $this->assertEquals(5000, $circuitBreakerConfigArray['sleepWindowInMilliseconds']);

        $this->assertArrayHasKey('forceOpen', $circuitBreakerConfigArray);
        $this->assertFalse($circuitBreakerConfigArray['forceOpen']);

        $this->assertArrayHasKey('forceClosed', $circuitBreakerConfigArray);
        $this->assertFalse($circuitBreakerConfigArray['forceClosed']);

        // metrics
        $this->assertArrayHasKey('metrics', $defaultConfigArray);
        $metricsConfigArray = $defaultConfigArray['metrics'];

        $this->assertArrayHasKey('healthSnapshotIntervalInMilliseconds', $metricsConfigArray);
        $this->assertEquals(1000, $metricsConfigArray['healthSnapshotIntervalInMilliseconds']);

        $this->assertArrayHasKey('rollingStatisticalWindowInMilliseconds', $metricsConfigArray);
        $this->assertEquals(1000, $metricsConfigArray['rollingStatisticalWindowInMilliseconds']);

        $this->assertArrayHasKey('rollingStatisticalWindowBuckets', $metricsConfigArray);
        $this->assertEquals(10, $metricsConfigArray['rollingStatisticalWindowBuckets']);
    }

    public function testChangedConfig()
    {
        $changedConfig = array(
            'fallback' => true,
            'requestCache' => false,
            'requestLog' => true,
            'circuitBreaker' => array(
                'errorThresholdPercentage' => 101,
                'forceOpen' => true,
                'forceClosed' => true,
                'requestVolumeThreshold' => 102,
                'sleepWindowInMilliseconds' => 103,
            ),
            'metrics' => array(
                'healthSnapshotIntervalInMilliseconds' => 104,
                'rollingStatisticalWindowInMilliseconds' => 105,
                'rollingStatisticalWindowBuckets' => 106,
            )
        );

        $container = new ContainerBuilder();
        $this->extension->load(array(array('default' => $changedConfig)), $container);

        $configArrayAll = $container->getParameter('phystrix.configuration.data');

        $this->assertArrayHasKey('default', $configArrayAll);

        $defaultConfigArray = $configArrayAll['default'];

        // fallback
        $this->assertArrayHasKey('fallback', $defaultConfigArray);
        $this->assertEquals(true, $defaultConfigArray['fallback']['enabled']);

        // requestCache
        $this->assertArrayHasKey('requestCache', $defaultConfigArray);
        $this->assertEquals(false, $defaultConfigArray['requestCache']['enabled']);

        // requestLog
        $this->assertArrayHasKey('requestLog', $defaultConfigArray);
        $this->assertEquals(true, $defaultConfigArray['fallback']['enabled']);

        // circuitBreaker
        $this->assertArrayHasKey('circuitBreaker', $defaultConfigArray);
        $circuitBreakerConfigArray = $defaultConfigArray['circuitBreaker'];

        $this->assertArrayHasKey('errorThresholdPercentage', $circuitBreakerConfigArray);
        $this->assertEquals(101, $circuitBreakerConfigArray['errorThresholdPercentage']);

        $this->assertArrayHasKey('requestVolumeThreshold', $circuitBreakerConfigArray);
        $this->assertEquals(102, $circuitBreakerConfigArray['requestVolumeThreshold']);

        $this->assertArrayHasKey('sleepWindowInMilliseconds', $circuitBreakerConfigArray);
        $this->assertEquals(103, $circuitBreakerConfigArray['sleepWindowInMilliseconds']);

        $this->assertArrayHasKey('forceOpen', $circuitBreakerConfigArray);
        $this->assertTrue($circuitBreakerConfigArray['forceOpen']);

        $this->assertArrayHasKey('forceClosed', $circuitBreakerConfigArray);
        $this->assertTrue($circuitBreakerConfigArray['forceClosed']);

        // metrics
        $this->assertArrayHasKey('metrics', $defaultConfigArray);
        $metricsConfigArray = $defaultConfigArray['metrics'];

        $this->assertArrayHasKey('healthSnapshotIntervalInMilliseconds', $metricsConfigArray);
        $this->assertEquals(104, $metricsConfigArray['healthSnapshotIntervalInMilliseconds']);

        $this->assertArrayHasKey('rollingStatisticalWindowInMilliseconds', $metricsConfigArray);
        $this->assertEquals(105, $metricsConfigArray['rollingStatisticalWindowInMilliseconds']);

        $this->assertArrayHasKey('rollingStatisticalWindowBuckets', $metricsConfigArray);
        $this->assertEquals(106, $metricsConfigArray['rollingStatisticalWindowBuckets']);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testConfigMustHaveDefault()
    {
        $container = new ContainerBuilder();
        $this->extension->load(array(array()), $container);
    }
}
