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

class CartEvent {
    const EVENT_CREATE_CART          = 'createCart';
    const EVENT_CREATE_CART_POST     = 'createCart.post';
    const EVENT_DELETE_CART          = 'removeCart';
    const EVENT_DELETE_CART_POST     = 'removeCart.post';
    const EVENT_EMPTY_CART           = 'emptyCart';
    const EVENT_EMPTY_CART_POST      = 'emptyCart.post';
}