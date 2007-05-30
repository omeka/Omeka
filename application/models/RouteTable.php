<?php 
/**
* RouteTable
*/
class RouteTable extends Doctrine_Table
{
	public function findStatic()
	{
		$conn = $this->getConnection();
		
		$res = $conn->execute("SELECT * FROM routes r WHERE r.active = 1 AND r.static = 1");
		$route_a = $res->fetchAll();
		
		$routes = array();
		foreach ($route_a as $route) {
			$routes[$route['name']] = array('name'=>$route['name'], 'route'=>$route['route'], 'path'=>$route['path']);
		}
		
		return $routes;
	}
}
 
?>
