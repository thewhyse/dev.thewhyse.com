<?php

declare(strict_types=1);

namespace ProjectHuddle\Vendor\Laminas\ServiceManager\Proxy;

use ProjectHuddle\Vendor\Laminas\ServiceManager\Exception;
use ProjectHuddle\Vendor\Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use ProjectHuddle\Vendor\ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProjectHuddle\Vendor\ProxyManager\Proxy\LazyLoadingInterface;
use ProjectHuddle\Vendor\ProxyManager\Proxy\VirtualProxyInterface;
use ProjectHuddle\Vendor\Psr\Container\ContainerInterface;

use function sprintf;

/**
 * Delegator factory responsible of instantiating lazy loading value holder proxies of
 * given services at runtime
 *
 * @link https://github.com/Ocramius/ProxyManager/blob/master/docs/lazy-loading-value-holder.md
 */
final class LazyServiceFactory implements DelegatorFactoryInterface
{
    /**
     * @param array<string, class-string> $servicesMap A map of service names to
     *     class names of their respective classes
     */
    public function __construct(private LazyLoadingValueHolderFactory $proxyFactory, private array $servicesMap)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param string $name
     * @return VirtualProxyInterface
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, ?array $options = null)
    {
        if (isset($this->servicesMap[$name])) {
            $initializer = static function (&$wrappedInstance, LazyLoadingInterface $proxy) use ($callback): bool {
                $proxy->setProxyInitializer(null);
                $wrappedInstance = $callback();

                return true;
            };

            return $this->proxyFactory->createProxy($this->servicesMap[$name], $initializer);
        }

        throw new Exception\ServiceNotFoundException(
            sprintf('The requested service "%s" was not found in the provided services map', $name)
        );
    }
}
