<?php 
/**
* RouteTable
*/
class RouteTable extends Doctrine_Table
{
	public function findStatic()
	{
		$q = new Doctrine_Query;
		$q->parseQuery("SELECT r.* FROM Route r WHERE r.active = 1 AND r.static = 1");
		$res = $q->execute(array(), Doctrine::FETCH_ARRAY);
		
		$routes = array();
		foreach ($res as $k => $v) {
			$routes[$v['r']['name']] = array('name'=>$v['r']['name'], 'route'=>$v['r']['route'], 'path'=>$v['r']['path']);
		}
		
		return $routes;
	}
}
 
?>
