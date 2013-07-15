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

    private function checkCart($products)
    {
    	if (!is_array($products) OR count($products) == 0)
    	{
    		throw new \Exception('Il metodo Insert vuole un array.');
    		return FALSE;
    	}

    	if (!isset($products['id']) OR !isset($products['qty']) OR !isset($products['price']) OR !isset($products['name']))
    	{
    		throw new \Exception('L\' array deve contenere id, qty, price, name in maniera permanente.');
    		return FALSE;
    	}

    	if (!is_numeric($products['qty']) OR $products['qty'] == 0)
    	{
    		throw new \Exception('La qty deve essere un numero interno e differtente da zero.');
    		return FALSE;
    	}

    	return TRUE;
    }

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
     * Aggiungo un prodotto al carrello
     *
     * @example $this->ZendCart()->insert($request->getPost());
     * @example $this->ZendCart()->insert(array(id => '', 'qty' => '', 'price' => ''));
     * @param array $products
     */
    public function insert($products = array())
    {
		if($this->checkCart($products))
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
        $token = (int) $products['token'];
        $max = count($this->_session['products']);
        for ($i = 0; $i < $max; $i ++) {
            if ($token == $this->_session['products'][$i]['token']) {
                $this->_session['products'][$i]['qty'] = $products['qty'];
                break;
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

    /**
     * Elimino tutti i prodotti dal carrello
     */
    public function destroy()
    {
        $this->_session->offsetUnset('products');
    }

    public function getCart()
    {
        return $products = $this->_session->offsetGet('products');
    }

    public function getItems()
    {
		return count($products = $this->_session->offsetGet('products'));
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
    	$result['total'] = number_format($price, 2);
    	$result['vat'] = number_format(($price/100) * $zendcart, 2);
    	$result['total_with_vat'] = number_format($price + $result['vat'], 2);
    	return $result;
    }
}