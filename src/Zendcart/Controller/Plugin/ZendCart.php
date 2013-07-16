<?php
/**
 * ZendCart
 * Simple Shopping Cart
 *
 * @package ZF2 Modules
 * @category Plugin
 * @copyright 2013
 * @version 1.0 Beta
 *
 * @author Vincenzo Provenza <info@ilovecode.it>
 * @author Concetto Vecchio  <info@cvsolutions.it>
 */
namespace ZendCart\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;

class ZendCart extends AbstractPlugin
{

    /**
     * @var $_session
     */
    private $_session;

    /**
     * @var $_config
     */
    private $_config;

    /**
     * __construct
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->_config = $config;
        $this->_session = new Container('zfProducts');
    }

    /**
     * Create the array of cart
     *
     * @param array $items
     * @access private
     * @return item products
     */
    private function _cart($items = array())
    {
    	return array(
            'id'		=> $items['id'],
            'qty' 		=> $items['qty'],
            'price' 	=> $this->_formatNumber($items['price']),
            'name' 		=> $items['name'],
        	'options'	=> isset($items['options']) ? $items['options'] : 0,
            'date' 	  	=> date('Y-m-d H:i:s', time())
        );
    }

    /**
     * Checks if the parameter is an array
     * and different from zero
     *
     * @param array $items
     * @access private
     * @return boolean
     */
    private function _isArray($items)
    {
        $items = (array) $items;
        if (!is_array($items) or count($items) == 0)
        {
            throw new \Exception('The method takes an array.');
            return FALSE;
        }
    }

    /**
     * Checks if the parameter is an array
     * and different from zero
     *
     * @param array $items
     * @access private
     * @return boolean
     */
    private function _isCartArray($items = array())
    {
        if (!is_array($items) or count($items) == 0)
        {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Checks if the parameter qty is numeric
     * and if it is different from zero
     *
     * @param array $items
     * @access private
     * @return boolean
     */
    private function _checkQty($items)
    {
        if (!is_numeric($items['qty']) or $items['qty'] == 0)
        {
            throw new \Exception('The parameter qty must be in numeric and different from zero.');
            return FALSE;
        }
    }

    /**
     * Verifies that the parameters are
     *
     * @param array $items
     * @access private
     * @return boolean
     */
    private function _checkCartInsert($items)
    {
        $this->_isArray($items);
        $this->_checkQty($items);
        if (!isset($items['id']) or ! isset($items['qty']) or ! isset($items['price']) or ! isset($items['name']))
        {
            throw new \Exception('The Insert method takes an array that must contain id, qty, price, name.');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Verifies that the token,qty
     * parameters exists
     *
     * @param array $items
     * @accessprivate
     * @return boolean
     */
    private function _checkCartUpdate($items)
    {
        $this->_isArray($items);
        $this->_checkQty($items);

        if (!isset($items['token']) or ! isset($items['qty']))
        {
            throw new \Exception('The Update method takes an array that must contain token.');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Verifies that the token
     * parameter exists
     *
     * @param array $items
     * @access private
     * @return boolean
     */
    private function _checkCartRemove($items)
    {
        if (!isset($items['token']))
        {
            throw new \Exception('The Remove method takes an array that must contain token.');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Check if there are options
     *
     * @param array $items
	 * @access	private
	 * @return boolean
     */
    private function _checkHasOption($token)
    {
    	if (!isset($this->_session['products'][$token]['options']) OR count($this->_session['products'][$token]['options']) == 0)
		{
			return FALSE;
		}
		return TRUE;
    }

    /**
     * Number_format for the price,
     * total, sub-total, vat.
     *
     * @param array $items
	 * @access	private
	 * @return	integer
     */
    private function _formatNumber($number)
    {
        if ($number == '')
        {
        	return FALSE;
        }
        return number_format($number, 2, '.', ',');
    }

    /**
     * Add a product to cart
     *
     * @example $this->ZendCart()->insert($request->getPost());
     * @example $this->ZendCart()->insert(array(id => '', 'qty' => '', 'price' => '', 'name' => ''));
     * @param array $items
     * @access public
     * @return null
     */
    public function insert($items = array())
    {
        if ($this->_checkCartInsert($items) === TRUE)
        {
        	$token = sha1($items['id'].$items['qty'].time());

            if (is_array($this->_session['products']))
            {
                $this->_session['products'][$token] = $this->_cart($items);
            } else {
                $this->_session['products'] = array();
                $this->_session['products'][$token] = $this->_cart($items);
            }
        }
    }

    /**
     * Update the quantity of a product
     *
     * @example $this->ZendCart()->update(array('token' => '', 'qty' => ''));
     * @param array $items
     * @access public
     * @return null
     */
    public function update($items = array())
    {
        if ($this->_checkCartUpdate($items) === TRUE)
        {
			$this->_session['products'][$items['token']]['qty'] = $items['qty'];
        }
    }

    /**
     * Delete the item from the cart
     *
     * @example $this->ZendCart()->remove(array('token' => ''));
     * @param array $items
     * @access public
     * @return null
     */
    public function remove($items = array())
    {
        if ($this->_checkCartRemove($items) === TRUE)
        {
        	unset($this->_session['products'][$items['token']]);
        }
    }

    /**
     * Delete all items from the cart
     *
     * @access public
     * @return null
     */
    public function destroy()
    {
        $this->_session->offsetUnset('products');
    }

    /**
     * Extracts all items from the cart
     *
     * @access public
     * @return array
     */
    public function cart()
    {
        $items = $this->_session->offsetGet('products');
        if ($this->_isCartArray($items) === TRUE)
        {
            $items = array();
            foreach ($this->_session->offsetGet('products') as $key => $value)
            {
                $items[$key] = array(
                    'id' 		=> 	$value['id'],
                    'qty' 		=> 	$value['qty'],
                    'price' 	=> 	$value['price'],
                    'name' 		=> 	$value['name'],
                    'sub_total'	=> 	$this->_formatNumber($value['price'] * $value['qty']),
                	'options' 	=> 	$value['options'],
                    'date' 		=> 	$value['date']
                );
            }
            return $items;
        }
    }

    /**
     * Counts the total number of
     * items in cart
     *
	 * @access	public
	 * @return	integer
     */
    public function total_items()
    {
        $total_items = 0;
        $items = $this->_session->offsetGet('products');
        if ($this->_isCartArray($items) === TRUE)
        {
            foreach ($items as $key)
            {
                $total_items =+ ($total_items + $key['qty']);
            }
            return $total_items;
        }
    }

    /**
     * Counts the total number of
     * items in cart
     *
     * @access public
     * @return array
     */
    public function total()
    {
        if ($this->_isCartArray($this->cart()) === TRUE)
        {
            $price = 0;
            foreach ($this->cart() as $key)
            {
                $price =+ ($price + ($key['price'] * $key['qty']));
            }

            $params = $this->_config['vat'];
            $vat = $this->_formatNumber((($price / 100) * $params));

            return array(
                'sub-total' => $this->_formatNumber($price),
                'vat' 		=> $vat,
                'total' 	=> $this->_formatNumber($price + $vat)
            );
        }
    }

    /**
     * item_options
     *
     * Returns the an array of options, for a particular product token.
     *
     * @access	public
     * @return	array
     */
    public function item_options($token)
    {
    	if($this->_checkHasOption($token) === TRUE)
    	{
    		return $this->_session['products'][$token]['options'];
    	}
    }
}