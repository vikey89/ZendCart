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
 * @author Stefano Corallo <stefanorg@gmail.com>
 */
namespace ZendCart\Event;

use Zend\EventManager\Event;

class CartEvent extends Event {

    const EVENT_ADD_ITEM             = 'addItem';
    const EVENT_ADD_ITEM_POST        = 'addItem.post';
    const EVENT_REMOVE_ITEM          = 'removeItem';
    const EVENT_REMOVE_ITEM_POST     = 'removeItem.post';
    const EVENT_CREATE_CART          = 'createCart';
    const EVENT_CREATE_CART_POST     = 'createCart.post';
    const EVENT_DELETE_CART          = 'removeCart';
    const EVENT_DELETE_CART_POST     = 'removeCart.post';
    const EVENT_EMPTY_CART           = 'emptyCart';
    const EVENT_EMPTY_CART_POST      = 'emptyCart.post';
    const EVENT_UPDATE_QUANTITY      = 'updateQuantities';
    const EVENT_UPDATE_QUANTITY_POST = 'updateQuantities.post';


    protected $cartId;
    protected $itemToken;
    protected $cartItem;


    /**
     * Gets the value of cartId.
     *
     * @return mixed
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * Sets the value of cartId.
     *
     * @param mixed $cartId the cart id
     *
     * @return self
     */
    public function setCartId($cartId)
    {
        $this->setParam('cartId', $cartId);
        $this->cartId = $cartId;

        return $this;
    }

    /**
     * Gets the value of cartId.
     *
     * @return mixed
     */
    public function getCartItem()
    {
        return $this->cartItem;
    }

    /**
     * Sets the value of cartId.
     *
     * @param mixed $cartId the cart id
     *
     * @return self
     */
    public function setCartItem($cartItem)
    {
        $this->setParam('cartItem', $cartItem);
        $this->cartItem = $cartItem;

        return $this;
    }

    /**
     * Gets the value of itemToken.
     *
     * @return mixed
     */
    public function getItemToken()
    {
        return $this->itemToken;
    }

    /**
     * Sets the value of itemToken.
     *
     * @param mixed $itemToken the item token
     *
     * @return self
     */
    public function setItemToken($itemToken)
    {
        $this->setParam('itemToken', $itemToken);
        $this->itemToken = $itemToken;

        return $this;
    }
}