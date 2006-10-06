<?php
/**
 * undocumented class
 *
 * @package default
 * @author Kris Kelly
 **/
class Kea_Plugin_Test implements Kea_Plugin_Interface
{
	
	public function install()
	{
		echo __CLASS__ . " is installed.";
		return TRUE;
	}
	
	/**
	 * This figures out what function to run based on the content of the message
	 *
	 * @return void
	 * @author Kris Kelly
	 **/
	public function update( Kea_Plugin_Manager $mgr, Kea_Plugin_Message &$msg )
	{

		//var_dump($msg);
		$result = $msg->getResult();
		if( $msg->isControllerMsg() )
		{
			foreach( $msg->getMethods() as $k=>$v )
			{
				$method = $msg->getController() . $v;

				if( method_exists($this, $method) )
				{
					
					//If the message contains a result from a previous function call, then that is the first argument (by convention)
					if( $msg->getResult() ) 
					{
						$args = array_merge( 
									array( 0 => $msg->getResult() ), 
									$msg->getMethodArgs($v) );
					}
					else $args = $msg->getMethodArgs($v);

					$msg->setResult( call_user_func_array( array( $this, $method ), $args ) );				
				}

			}
		}
	}
	
	public function uninstall()
	{
		echo __CLASS__ . " is uninstalled.";
		return TRUE;
	}
	
	public function ItemsControllerXML( $item )
	{
		if(!$item) throw new Kea_Plugin_Exception ( 'Item given is invalid');
			if( $item instanceof Item_Collection ) $item = $item->getObjectAt(0);
			
			$res = '<item>';
			foreach( $item as $key => $value )
			{
				$res .= '<'.$key .'> ' . $value . ' </' . $key . '>';
			}
			$res .= '</item>';			
		return $res;
	}
	
	public function ItemsControllerfindById( $result, $id )
	{
		$result->kablooie = 'Oh no!  Kablooie!';
		return $result;
	}
} // END class 
?>