<?php
namespace ZendCart\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendCart\Controller\Plugin\ZendCart;

class ZendCartFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $servicelocator)
    {
        $allServices = $servicelocator->getServiceLocator();
        $config = $allServices->get('ServiceManager')->get('Configuration');

        if (!isset($config['zendcart']))
        {
            throw new \Exception('Configuration ZendCart not set.');
        }

        if (!isset($config['zendcart']['vat']))
        {
            throw new \Exception('No vat index defined.');
        }

        $default = array(
            'on_insert_update_existing_item' => false
        );

        $options = array_merge($default, $config['zendcart']);

        return new ZendCart($options);
    }
}