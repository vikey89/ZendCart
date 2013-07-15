<?php
namespace ZendCart\Controller\Plugin;

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
     * @param array $items
     * @return item products
     */
    private function _cart($items = array())
    {
        return array(
            'id' => $items['id'],
            'qty' => $items['qty'],
            'price' => $this->_formatNumber($items['price']),
            'name' => $items['name'],
            'token' => rand(),
            'date' => date('Y-m-d H:i:s', time())
        );
    }

    private function _isArray($items)
    {
        $items = (array) $items;
        if (! is_array($items) or count($items) == 0) {
            throw new \Exception('Il metodo vuole un array.', 1);
            return FALSE;
        }
    }

    private function _isCartArray($items = array())
    {
        if (! is_array($items) or count($items) == 0) {
            return FALSE;
        }
        return TRUE;
    }

    private function _checkQty($items)
    {
        if (! is_numeric($items['qty']) or $items['qty'] == 0) {
            throw new \Exception('La qty deve essere un numero interno e differtente da zero.', 2);
            return FALSE;
        }
    }

    private function _checkCartInsert($items)
    {
        $this->_isArray($items);
        $this->_checkQty($items);
        if (! isset($items['id']) or ! isset($items['qty']) or ! isset($items['price']) or ! isset($items['name'])) {
            throw new \Exception('Il metodo Insert vuole un array che deve contenere id, qty, price, name in maniera permanente.', 3);
            return FALSE;
        }
        return TRUE;
    }

    private function _checkCartUpdate($items)
    {
        $this->_isArray($items);
        $this->_checkQty($items);
        
        if (! isset($items['token']) or ! isset($items['qty'])) {
            throw new \Exception('Il metodo Update vuole un array che deve contenere token, qty in maniera permanente.', 4);
            return FALSE;
        }
        return TRUE;
    }

    private function checkCartRemove($items)
    {
        if (! isset($items['token'])) {
            throw new \Exception('Il metodo Remove vuole un array che deve contenere token in maniera permanente.', 5);
            return FALSE;
        }
        return TRUE;
    }

    private function _formatNumber($number)
    {
        if ($number == '')
            return FALSE;
        return number_format($number, 2, '.', ',');
    }

    /**
     * Aggiungo un prodotto al carrello
     *
     * @example $this->ZendCart()->insert($request->getPost());
     * @example $this->ZendCart()->insert(array(id => '', 'qty' => '', 'price' => ''));
     * @param array $items
     */
    public function insert($items = array())
    {
        if ($this->_checkCartInsert($items)) {
            
            if (is_array($this->_session['products'])) {
                $max = count($this->_session['products']);
                $this->_session['products'][$max] = $this->_cart($items);
            } else {
                $this->_session['products'] = array();
                $this->_session['products'][0] = $this->_cart($items);
            }
        }
    }

    /**
     * Aggiorno la quantitˆ di un prodotto
     *
     * @example $this->ZendCart()->update(array('token' => '', 'qty' => ''));
     * @param array $items
     */
    public function update($items = array())
    {
        if ($this->_checkCartUpdate($items)) {
            $token = (int) $items['token'];
            $max = count($this->_session['products']);
            for ($i = 0; $i < $max; $i ++) {
                if ($token == $this->_session['products'][$i]['token']) {
                    $this->_session['products'][$i]['qty'] = $items['qty'];
                    break;
                }
            }
        }
    }

    /**
     * Elimino il prodotto dal carrello
     *
     * @example $this->ZendCart()->remove(array('token' => ''));
     * @param array $items
     */
    public function remove($items = array())
    {
        if ($this->checkCartRemove($items)) {
            $token = (int) $items['token'];
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

    public function cart()
    {
        $items = $this->_session->offsetGet('products');
        if ($this->_isCartArray($items) === TRUE) {
            $items = array();
            foreach ($this->_session->offsetGet('products') as $key) {
                $items[] = array(
                    'id' => $key['id'],
                    'qty' => $key['qty'],
                    'price' => $key['price'],
                    'name' => $key['name'],
                    'sub_total' => $this->_formatNumber($key['price'] * $key['qty']),
                    'token' => $key['token'],
                    'date' => $key['date']
                );
            }
            print_r($items);
            return $items;
        }
    }

    public function total_items()
    {
        $total_items = 0;
        $items = $this->_session->offsetGet('products');
        if ($this->_isCartArray($items) === TRUE) {
            foreach ($items as $key) {
                $total_items = + ($total_items + $key['qty']);
            }
            return $total_items;
        }
    }

    public function total()
    {
        if ($this->_isCartArray($this->cart()) === TRUE) {
            $price = 0;
            foreach ($this->cart() as $key) {
                $price = + $price + ($key['price'] * $key['qty']);
            }
            
            $params = $this->_config['iva'];
            $vat = $this->_formatNumber((($price / 100) * $params));
            
            return array(
                'total' => $this->_formatNumber($price),
                'vat' => $vat,
                'total_with_vat' => $this->_formatNumber($price + $vat)
            );
        }
    }
}