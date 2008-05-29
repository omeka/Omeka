<?php
Mock::generate('Omeka_Plugin_Broker', 'AbstractMock_Omeka_Plugin_Broker');
class Mock_Plugin_Broker extends AbstractMock_Omeka_Plugin_Broker
{
	private $hookCount = 0;
	
	public function expectHooks($hooks, $args=null)
	{
		foreach ($hooks as $key => $hook) {
			$hook_args = $args ? array($hook, $args) : array($hook);
			$this->expectAt($this->hookCount, '__call', $hook_args);
			$this->hookCount++;
		}
	}	
}

Mock::generate('Omeka_Db', 'AbstractMockOmeka_Db');

//Extend the mock DB class with convenience methods for checking SQL statements
class MockOmeka_Db extends AbstractMockOmeka_Db
{
	public function quote($text)
	{
		return "'" . $text . "'";
	}
	
	public function expectCountQuery($sql)
	{
		$this->expect(
					'fetchOne', 
					array(new IdenticalSqlExpectation($sql) ) );		
	}
	
	public function query($sql, $params=array())
	{
	    
	}
	
	public function expectQuery($sql, $params=array())
	{
		$this->expectAtLeastOnce('query', 
			array(new IdenticalSqlExpectation($sql), $params) );
	}

	/**
	 * @param mixed bool|object
	 *
	 * @return void
	 **/
	public function setTable($record_class, $table_is_mock=true)
	{
		//Determine the class of the table to instantiate
		$table_class = $record_class . 'Table';
			
		if(!class_exists($table_class)) {
			$table_class = "Omeka_Db_Table";
		}
		
		//We should set up a mock table
		if($table_is_mock === true) {
			Mock::generate($table_class);
			$mock_table_class = "Mock" . $table_class;
			
			$table = new $mock_table_class;
		}
		//We should set up an actual table instance
		elseif($table_is_mock === false) {
			$table = new $table_class($record_class);
		}
		//We are passed an actual object
		else {
			$table = $table_is_mock;
		}
		$this->setReturnValue('getTable', $table, array($record_class));
	}
}