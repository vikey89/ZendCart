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
        
        if (! isset($config['zendcart'])) {
            throw new \Exception('Configurazione ZendCart non impostata.');
        }
        return new ZendCart(array(
            'iva' => $config['zendcart']['iva']
        ));
    }
}