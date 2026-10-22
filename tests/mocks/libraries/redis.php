<?php

// In-memory stand-in for phpredis, covering only what CI_Cache_redis calls
class Mock_Libraries_Redis {

	public $hashes = array();
	public $expires = array();

	public function hMSet($key, $data)
	{
		$this->hashes[$key] = $data;
		return TRUE;
	}

	public function hMGet($key, $fields)
	{
		if (isset($this->expires[$key]) && $this->expires[$key] <= time())
		{
			unset($this->hashes[$key], $this->expires[$key]);
		}

		$result = array();
		foreach ($fields as $field)
		{
			$result[$field] = isset($this->hashes[$key][$field]) ? $this->hashes[$key][$field] : FALSE;
		}

		return $result;
	}

	public function expireAt($key, $timestamp)
	{
		$this->expires[$key] = $timestamp;
		return TRUE;
	}

	public function sIsMember($key, $member)
	{
		return FALSE;
	}

	public function sRem($key, $member)
	{
		return 0;
	}

	public function close()
	{
		return TRUE;
	}

}
