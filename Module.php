<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Zendcart for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zendcart;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zendcart\Controller\Plugin\ZendCart;

class Module implements AutoloaderProviderInterface
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__)
                )
            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'ZendCart' => function ($sm)
                {
                    $serviceLocator = $sm->getServiceLocator();
                    $config = $serviceLocator->get('Configuration');
                    if (!isset($config['zendcart'])) {
                        throw new \Exception('Configurazione ZendCart non impostata.');
                    }
                    $cart = new ZendCart(array(
                        'iva' => $config['zendcart']['iva']
                    ));
                    return $cart;
                }
            )
        );
    }
}
