<?php

/**
 * 1) insert:
 * verifica array sulla singola kiave
 * aggiunta alla sessione
 * recupero il valore per getTotal()
 * 
 * 2) update:
 * verifica array sulla singola kiave
 * aggiorna la sessione
 * se la qty  == 0 elimina il prodotto dalla sessione
 * recupero il valore per getTotal()
 * 
 * 3) getCart:
 *  
 */



namespace Zendcart\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;

class ZendCart extends AbstractPlugin
{
    private $_cart;
    private $_config;
    // array('id' => '', 'qty' => '', 'min_qty' => null, 'max_qty' => null, 'price' => '', 'name' => '', 'options' => array('')),
    
    public function __construct($config = array()){
        $this->_config = $config;
        $this->_cart = new Container('cart');
    }
    
    public function insert($prodotti){
        echo $this->_config['iva'];
        $this->_cart->offsetSet('data', $prodotti);
        $email = $this->_cart->offsetGet('data');
        print_r($email);
    }
    
    public function update(){
    
    }
    
    public function destroy(){
    $this->_cart->offsetUnset('cart');
    }
    
    public function getCart(){
    
    }
    
    public function getTotal(){
    
    }
}