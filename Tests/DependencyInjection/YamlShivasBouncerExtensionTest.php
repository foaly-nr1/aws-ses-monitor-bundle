<?php
namespace Shivas\BouncerBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class YamlShivasBouncerExtensionTest extends AbstractShivasBouncerExtensionTest
{
    protected function loadConfiguration(ContainerBuilder $container, $resource)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Fixtures/'));
        $loader->load($resource.'.yml');
    }
}
