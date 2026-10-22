<?php

class Cart_test extends CI_TestCase {

	public function set_up()
	{
		defined('UTF8_ENABLED') OR define('UTF8_ENABLED', FALSE);

		$loader = $this->getMockBuilder('CI_Loader')->setMethods(array('driver'))->getMock();
		$this->ci_instance_var('load', $loader);
		$this->ci_instance_var('session', new Mock_Libraries_Cartsession());

		$this->cart = new CI_Cart();
	}

	// --------------------------------------------------------------------

	public function test_insert_adds_to_total()
	{
		$rowid = $this->cart->insert(array('id' => 'sku_1', 'qty' => 2, 'price' => 9.95, 'name' => 'T-Shirt'));

		$this->assertNotFalse($rowid);
		$this->assertEquals(19.9, $this->cart->total());
		$this->assertEquals(2, $this->cart->total_items());
	}

	// --------------------------------------------------------------------

	public function test_insert_same_item_adds_quantities()
	{
		$this->cart->insert(array('id' => 'sku_1', 'qty' => 0.5, 'price' => 10, 'name' => 'Cheese'));
		$this->cart->insert(array('id' => 'sku_1', 'qty' => 0.5, 'price' => 10, 'name' => 'Cheese'));

		$this->assertEquals(1, $this->cart->total_items());
		$this->assertEquals(10, $this->cart->total());
	}

	// --------------------------------------------------------------------

	public function test_insert_rejects_zero_and_negative_quantity()
	{
		$this->assertFalse($this->cart->insert(array('id' => 'sku_1', 'qty' => 0, 'price' => 10, 'name' => 'Free')));
		$this->assertFalse($this->cart->insert(array('id' => 'sku_1', 'qty' => -3, 'price' => 10, 'name' => 'Refund')));
		$this->assertEquals(0, $this->cart->total());
	}

	// --------------------------------------------------------------------

	public function test_update_with_zero_or_negative_quantity_removes_item()
	{
		$keep = $this->cart->insert(array('id' => 'sku_1', 'qty' => 1, 'price' => 50, 'name' => 'Shoes'));
		$drop = $this->cart->insert(array('id' => 'sku_2', 'qty' => 1, 'price' => 20, 'name' => 'Socks'));

		$this->assertTrue($this->cart->update(array('rowid' => $drop, 'qty' => -3)));
		$this->assertFalse($this->cart->get_item($drop));
		$this->assertEquals(50, $this->cart->total());

		$this->assertTrue($this->cart->update(array('rowid' => $keep, 'qty' => 0)));
		$this->assertEquals(0, $this->cart->total());
	}

}
