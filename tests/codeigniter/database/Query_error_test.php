<?php

class Query_error_test extends CI_TestCase {

	/**
	 * @var object Database/Query Builder holder
	 * @see ./mocks/schema/skeleton.php
	 */
	protected $db;

	public function set_up()
	{
		// Other drivers raise PHP warnings on failed queries, which the test config turns into exceptions
		if (strpos(DB_DRIVER, 'pdo') !== 0)
		{
			$this->markTestSkipped('Only PDO drivers are covered here.');
		}

		$this->db = Mock_Database_Schema_Skeleton::init(DB_DRIVER);

		Mock_Database_Schema_Skeleton::create_tables();

		$this->db->truncate('job');
	}

	// ------------------------------------------------------------------------

	public function test_failed_query_returns_false()
	{
		$this->assertFalse($this->db->query('SELECT * FROM table_that_does_not_exist'));

		$error = $this->db->error();
		$this->assertNotEquals('', $error['message']);
	}

	// ------------------------------------------------------------------------

	public function test_failed_query_rolls_back_transaction()
	{
		$this->db->trans_start();
		$this->db->insert('job', array('id' => 1, 'name' => 'Grocery Sales', 'description' => 'Discount!'));
		$this->db->query('SELECT * FROM table_that_does_not_exist');

		$this->assertFalse($this->db->trans_complete());
		$this->assertEquals(0, $this->db->count_all('job'));

		// The transaction is closed, so the next one commits normally
		$this->db->trans_start();
		$this->db->insert('job', array('id' => 2, 'name' => 'Developer', 'description' => 'Awesome job'));
		$this->assertTrue($this->db->trans_complete());
		$this->assertEquals(1, $this->db->count_all('job'));
	}

}
