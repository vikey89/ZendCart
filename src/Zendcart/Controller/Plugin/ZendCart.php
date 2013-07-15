<?php
namespace Zendcart\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;

/**
 * ZendCart
 * Simple Shopping Cart
 *
 * @package ZF2 Modules
 * @category Plugin
 * @copyright 2013
 * @version 1.0 Beta
 *
 */
class ZendCart extends AbstractPlugin
{

    private $_session;

    private $_config;

    /**
     * __construct
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->_config = $config;
        $this->_session = new Container('zfPproducts');
    }

    /**
     * Appendo l'array del carrello
     *
     * @param array $products
     * @return item products
     */
    private function append_item($products = array())
    {
    	return array(
    			'id' => $products['id'],
    			'qty' => $products['qty'],
    			'price' => $products['price'],
    			'name' => $products['name'],
    			'token' => rand(),
    			'date' => date('Y-m-d H:i:s', time())
    	);
    }

    private function isArray($items)
    {
    	if (!is_array($items) OR count($items) == 0)
    	{
    		throw new \Exception('Il metodo vuole un array.');
    		return FALSE;
    	}
    }

    private function isQtyOk($items)
    {
    	if (!is_numeric($items['qty']) OR $items['qty'] == 0)
    	{
    		throw new \Exception('La qty deve essere un numero interno e differtente da zero.');
    		return FALSE;
    	}
    }

    private function checkCartInsert($items)
    {
    	$this->isArray($items);
    	$this->isQtyOk($items);
    	if (!isset($items['id']) OR !isset($items['qty']) OR !isset($items['price']) OR !isset($items['name']))
    	{
    		throw new \Exception('Il metodo Insert vuole un array che deve contenere id, qty, price, name in maniera permanente.');
    		return FALSE;
    	}
    	return TRUE;
    }

    private function checkCartUpdate($items)
    {
    	$this->isArray($items);
    	$this->isQtyOk($items);

    	if (!isset($items['token']) OR !isset($items['qty']))
    	{
    		throw new \Exception('Il metodo Update vuole un array che deve contenere token, qty in maniera permanente.');
    		return FALSE;
    	}
    	return TRUE;
    }

    private function checkCartRemove($items)
    {
        if (!isset($items['token']))
    	{
    		throw new \Exception('Il metodo Remove vuole un array che deve contenere token in maniera permanente.');
    		return FALSE;
    	}
    	return TRUE;
    }

    /**
     * Aggiungo un prodotto al carrello
     *
     * @example $this->ZendCart()->insert($request->getPost());
     * @example $this->ZendCart()->insert(array(id => '', 'qty' => '', 'price' => ''));
     * @param array $products
     */
    public function insert($products = array())
    {
		if($this->checkCartInsert($products))
		{
			if (is_array($this->_session['products'])) {
				$max = count($this->_session['products']);
				$this->_session['products'][$max] = $this->append_item($products);
			} else {
				$this->_session['products'] = array();
				$this->_session['products'][0] = $this->append_item($products);
			}
		}
    }

    /**
     * Aggiorno la quantitˆ di un prodotto
     *
     * @example $this->ZendCart()->update(array('token' => '', 'qty' => ''));
     * @param array $products
     */
    public function update($products = array())
    {
    	if($this->checkCartUpdate($products))
    	{
    		$token = (int) $products['token'];
    		$max = count($this->_session['products']);
    		for ($i = 0; $i < $max; $i ++) {
    			if ($token == $this->_session['products'][$i]['token']) {
    				$this->_session['products'][$i]['qty'] = $products['qty'];
    				break;
    			}
    		}
    	}
    }

    /**
     * Elimino il prodotto dal carrello
     *
     * @example $this->ZendCart()->remove(array('token' => ''));
     * @param array $products
     */
    public function remove($products = array())
    {
    	if($this->checkCartRemove($products))
    	{
    		$token = (int) $products['token'];
    		$max = count($this->_session['products']);
    		for ($i = 0; $i < $max; $i ++) {
    			if ($token == $this->_session['products'][$i]['token']) {
    				unset($this->_session['products'][$i]);
    				break;
    			}
    		}
    		$this->_session['products'] = array_values($this->_session['products']);
    	}
    }

    /**
     * Elimino tutti i prodotti dal carrello
     */
    public function destroy()
    {
        $this->_session->offsetUnset('products');
    }

    public function getCart()
    {
    	$products = array();
    	foreach ($this->_session->offsetGet('products') as $key)
    	{
    		$products[] = array(
    			'id' 		=> $key['id'],
    			'qty' 		=> $key['qty'],
    			'price' 	=> $key['price'],
    			'name'  	=> $key['name'],
    			'sub_total' => 	number_format($key['price'] * $key['qty'], 2),
    			'token' 	=> $key['token']
    		);
    	}
        return $products;
    }

    public function getItems()
    {
    	$items = 0;
    	foreach ($this->_session->offsetGet('products') as $key)
    	{
    		$items =+ $items + $key['qty'];
    	}
    	return $items;
    }

    public function getTotal()
    {
    	$price = 0;
    	foreach($this->getCart() as $key)
    	{
    		$price =+ $price + ($key['price'] * $key['qty']);
    	}

    	$config = $this->getController()->getServiceLocator()->get('Config');
    	$zendcart = $config['zendcart']['iva'];

    	$total['total'] = number_format($price, 2);
    	$total['vat'] = number_format(($price/100) * $zendcart, 2);
    	$total['total_with_vat'] = number_format($price + $total['vat'], 2);
    	return $total;
    }
}