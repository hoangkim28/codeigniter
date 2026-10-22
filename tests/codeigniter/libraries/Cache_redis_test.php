<?php

class Cache_redis_test extends CI_TestCase {

	public function set_up()
	{
		// CI_Driver lives in the same file as CI_Driver_Library
		class_exists('CI_Driver_Library');

		$class = new ReflectionClass('CI_Cache_redis');
		$this->cache = $class->newInstanceWithoutConstructor();
		$this->redis = new Mock_Libraries_Redis();

		$redis = $class->getProperty('_redis');
		$srem  = $class->getProperty('_sRemove_name');
		if (PHP_VERSION_ID < 80100)
		{
			$redis->setAccessible(TRUE);
			$srem->setAccessible(TRUE);
		}

		$redis->setValue($this->cache, $this->redis);
		$srem->setValue(NULL, 'sRem');
	}

	// --------------------------------------------------------------------

	public function test_save_without_ttl_never_expires()
	{
		$this->assertTrue($this->cache->save('forever', 'value', 0));
		$this->assertArrayNotHasKey('forever', $this->redis->expires);
		$this->assertEquals('value', $this->cache->get('forever'));
	}

	// --------------------------------------------------------------------

	public function test_save_with_ttl_sets_expiry()
	{
		$this->assertTrue($this->cache->save('short', 'value', 60));
		$this->assertGreaterThanOrEqual(time() + 59, $this->redis->expires['short']);
	}

}
