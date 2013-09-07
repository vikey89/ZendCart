[![Total Downloads](https://poser.pugx.org/zendcart/zendcart/downloads.png)](https://packagist.org/packages/zendcart/zendcart)
ZendCart
============================
Version 1.0

This model allows you to manage a shopping cart for e-commerce in an easy, simple and fast.

Installation
------------
For the installation uses composer [composer](http://getcomposer.org "composer - package manager").

```sh
php composer.phar require  zendcart/zendcart:dev-master
```

Add this project in your composer.json:


    "require": {
        "zendcart/zendcart": "dev-master"
    }
    

Post Installation
------------
Configuration:
- Add the module of `config/application.config.php` under the array `modules`, insert `ZendCart`
- Create a file named `zendcart.global.php` under `config/autoload/`. 
- Add the following lines to the file you just created:

```php
<?php
return array(
    'zendcart' => array(
        'vat'  => 21
    ),
);
```

Example
=====================================
Insert
------------
```php
$product = array(
    'id'      => 'cod_123abc',
    'qty'     => 1,
    'price'   => 39.95,
    'name'    => 'T-Shirt',
    'options' => array('Size' => 'M', 'Color' => 'Black')
);
$this->ZendCart()->insert($product);
```

Update
------------
```php
$product = array(
    'token' => '4b848870240fd2e976ee59831b34314f7cfbb05b',
    'qty'   => 2
);
$this->ZendCart()->update($product);
```

Remove
------------
```php
$product = array(
    'token' => '4b848870240fd2e976ee59831b34314f7cfbb05b',
);
$this->ZendCart()->remove($product);
```

Destroy
------------
```php
$this->ZendCart()->destroy();
```

Cart
------------
```php
$this->ZendCart()->cart();
```

Total
------------
```php
$this->ZendCart()->total();
```

Total Items
------------
```php
$this->ZendCart()->total_items();
```

Items Options
------------
```php
$this->ZendCart()->item_options('4b848870240fd2e976ee59831b34314f7cfbb05b');
```

Example in view
------------
Controller
```php
return new ViewModel(array(
    'items' => $this->ZendCart()->cart(),
    'total_items' => $this->ZendCart()->total_items(),
    'total' => $this->ZendCart()->total(),
));
```
View
```html
<?php if($total_items > 0): ?>
<h3>Products in cart (<?php echo $total_items; ?>):</h3>
<table style="width: 900px;" border="1">
<tr>
  <th>Qty</th>
  <th>Name</th>
  <th>Item Price</th>
  <th>Sub-Total</th>
</tr>
<?php foreach($items as $key):?>
<tr>
    <td style="text-align: center;"><?php echo $key['qty']; ?></td>
	<td style="text-align: center;">
	<?php echo $key['name']; ?>
		<?php if($key['options'] != 0):?>
			Options:
			<?php foreach($key['options'] as $options => $value):?>
				<?php echo $options.' '.$value;?>
			<?php endforeach;?>
		<?php endif;?>
	</td>
	<td style="text-align: center;"><?php echo $key['price']; ?></td>
	<td style="text-align: center;"><?php echo $key['sub_total']; ?></td>
</tr>
<?php endforeach;?>
<tr>
  <td colspan="2"></td>
  <td style="text-align: center;"><strong>Sub Total</strong></td>
  <td style="text-align: center;"> <?php echo $total['sub-total'];?></td>
</tr>
<tr>
  <td colspan="2"></td>
  <td style="text-align: center;"><strong>Vat</strong></td>
  <td style="text-align: center;"> <?php echo $total['vat'];?></td>
</tr>
<tr>
  <td colspan="2"></td>
  <td style="text-align: center;"><strong>Total</strong></td>
  <td style="text-align: center;"> <?php echo $total['total'];?></td>
</tr>

<?php else: ?>
<h4>The Shopping Cart Empty</h4>
<?php endif;?>
```

Function Reference
------------
<table>
    <tr>
    <td>Function</td>
    <td>Description</td></tr>
    <tr><td>$this->ZendCart()->insert();</td><td>Add a product to cart.</td></tr>
    <tr><td>$this->ZendCart()->update();</td><td>Update the quantity of a product.</td></tr>
    <tr><td>$this->ZendCart()->remove();</td><td>Delete the item from the cart.</td></tr>
    <tr><td>$this->ZendCart()->destroy();</td><td>Delete all items from the cart.</td></tr>
    <tr><td>$this->ZendCart()->cart();</td><td>Extracts all items from the cart.</td></tr>
    <tr><td>$this->ZendCart()->total();</td><td>Counts the total number of items in cart</td></tr>
    <tr><td>$this->ZendCart()->total_items();</td><td>Counts the total number of items in cart</td></tr>
    <tr><td>$this->ZendCart()->item_options();</td><td>Returns the an array of options, for a particular product token.</td></tr>
    <tr><td>Config Vat</td><td>Set your vat in zendcart.global.php</td></tr>
</table>

Contributors
=====================================

* Concetto Vecchio - info@cvsolutions.it
