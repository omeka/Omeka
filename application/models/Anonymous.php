<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'Entity.php';

/**
 * Anonymous
 *
 * This class is meant to account for entities that end up in the table without 
 * first name, last name, email, or institution name.  Basically they are blank placeholder
 * entries, which may be a bad idea anyway but I'm not totally sure.  Try to avoid using if possible
 * 
 * @package: Omeka
 */
class Anonymous extends Entity
{
	public function beforeSave()
	{
		$this->type = "Anonymous";
	}
	
	public function getName()
	{
		return 'Anonymous';
	}
}