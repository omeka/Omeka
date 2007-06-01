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
}
 
?>
