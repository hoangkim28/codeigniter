<?php

// Minimal session stand-in for CI_Cart, which only reads and writes userdata
class Mock_Libraries_Cartsession {

	public $data = array();

	public function userdata($key)
	{
		return isset($this->data[$key]) ? $this->data[$key] : NULL;
	}

	public function set_userdata($data)
	{
		$this->data = array_merge($this->data, $data);
	}

	public function unset_userdata($key)
	{
		unset($this->data[$key]);
	}

}
