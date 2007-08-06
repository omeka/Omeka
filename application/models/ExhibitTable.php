<?php 
/**
* Exhibit Table
*/
class ExhibitTable extends Doctrine_Table
{
	public function findBySlug($slug)
	{
		$q = $this->createQuery();
		$q->where('Exhibit.slug = ?',array($slug));
		return $q->execute()->getFirst();
	}
	
	public function findBy($params=array())
	{
		$dql = "SELECT e.* FROM Exhibit e";
		
		$q = new Doctrine_Query;
		
		$q->parseQuery($dql);
		
		if(isset($params['tags'])) {
			$tags = explode(',', $params['tags']);
			$q->innerJoin('e.ExhibitTaggings et');
			$q->innerJoin('et.Tag t');
			foreach ($tags as $k => $tag) {
				$q->addWhere('t.name = ?', trim($tag));
			}
		}
		
		if(isset($params['public'])) {
			$q->addWhere('e.public = 1');
		}
		
	//	echo $q;
		$exhibits = $q->execute();
		return $exhibits;
	}
}
 
?>
