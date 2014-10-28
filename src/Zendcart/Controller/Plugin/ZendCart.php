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
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use ZendCart\Event\CartEvent;

class ZendCart extends AbstractPlugin implements EventManagerAwareInterface
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
     * @var $eventManager
     */
    protected $eventManager;

    /**
     * __construct
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->_config = $config;
        $this->_session = new Container('zfProducts');
        $this->setEventManager(new EventManager());
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
            'price' 	=> $items['price'],
            'name' 		=> $items['name'],
        	'options'	=> isset($items['options']) ? $items['options'] : 0,
            'date' 	  	=> date('Y-m-d H:i:s', time()),
            'vat'       => isset($items['vat']) ? $items['vat'] : $this->_config['vat']
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

            $isNew = true;
            $shouldUpdate = $this->_config['on_insert_update_existing_item'];

            //check if should update existing product
            if($shouldUpdate){
                $products = is_array($this->_session['products']) ? $this->_session['products'] : array();
                foreach ($products as $token => $existing_item) {
                    if($existing_item['id'] === $items['id']){
                        //fount same product already on cart
                        $isNew = false;
                        $items = array('token'=>$token, 'qty'=> $existing_item['qty']+$items['qty']);
                        break;
                    }
                }
            }

            if($isNew){
                $token = sha1($items['id'].$items['qty'].time());

                if (is_array($this->_session['products']))
                {
                    $this->_session['products'][$token] = $this->_cart($items);
                } else {
                    //creo il carrello in sessione
                    $this->_session['products'] = array();
                    $this->_session->cartId = $this->_session->getManager()->getId();
                    $this->getEventManager()->trigger(CartEvent::EVENT_CREATE_CART_POST, $this, array('cart_id'=>$this->_session->cartId));
                    //aggiungo elemento
                    $this->_session['products'][$token] = $this->_cart($items);
                }
                //evento per elemento aggiunto
                $this->trigger(CartEvent::EVENT_ADD_ITEM_POST, $token, $this->_session['products'][$token], $this);
            }else{
                //update existing product
                $this->update($items);
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
            $this->trigger(CartEvent::EVENT_UPDATE_QUANTITY_POST, $items['token'], $this->_session['products'][$items['token']], $this);
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
        if (($this->_checkCartRemove($items) === TRUE) && isset($this->_session['products'][$items['token']]) )
        {
            $cart = $this->_session['products'][$items['token']];
            unset($this->_session['products'][$items['token']]);
            $this->trigger(CartEvent::EVENT_REMOVE_ITEM_POST, $items['token'], $cart, $this);
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
        $this->getEventManager()->trigger(CartEvent::EVENT_DELETE_CART_POST, $this, ['cart_id'=>$this->_session->cartId]);
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
                    'sub_total'	=> 	$value['price'] * $value['qty'],
                	'options' 	=> 	$value['options'],
                    'date' 		=> 	$value['date'],
                    'vat'       =>  $value['vat']
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
            $vat = 0;
            foreach ($this->cart() as $key)
            {
                $item_price = ($key['price'] * $key['qty']);
                $item_vat   = (($item_price/100)*$key['vat']);
                // $price =+ ($price + ($key['price'] * $key['qty']));
                $price += $item_price;
                $vat   += $item_vat;
            }

            return array(
                'sub_total' => $price,
                'vat' 		=> $vat,
                'total' 	=> $price + $vat
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

    public function getEventManager()
    {
        return $this->eventManager;
    }

    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(
            'ZendCart\Service\Cart',
            __CLASS__,
            get_called_class(),
            'zendcart'
        );
        // $eventManager->setEventClass('ZendCart\Service\Cart');

        $this->eventManager = $eventManager;
        return $this;
    }


    private function trigger($name, $token, $cartItem, $target=null)
    {
        $cartId = $this->_session->cartId;
        $event = new CartEvent();
        $event->setCartId($cartId)
            ->setItemToken($token)
            ->setCartItem($cartItem);

        if ($target)
            $event->setTarget($target);

        $this->getEventManager()->trigger($name, $event);
    }
}