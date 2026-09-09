<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Db\Table
 */
class Table_UsersActivations extends Omeka_Db_Table
{
    const VALIDITY_SECONDS = 86400;

    public function findByUrl($url)
    {
        $earliest = date('Y-m-d H:i:s', time() - self::VALIDITY_SECONDS);
        $select = $this->getSelect()->where('url = ?', $url)->where('added > ?', $earliest)->limit(1);
        return $this->fetchObject($select);
    }

    public function findByUser($user)
    {
        $select = $this->getSelect();
        $select->where('user_id = ?', $user->id);
        $select->limit(1);
        return $this->fetchObject($select);
    }

    public function deleteAllByUser($user)
    {
        if (!$user || !$user->id) {
            throw new InvalidArgumentException('Cannot delete activations for nonexistent user');
        }
        return $this->getDb()->delete($this->getTableName(), [
            'user_id = ?' => (int) $user->id,
        ]);
    }
}
