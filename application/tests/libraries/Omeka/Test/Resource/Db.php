<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 **/

class Omeka_Test_Resource_Db extends Zend_Application_Resource_Db
{
    public function init()
    {        
        $config = $this->_loadConfig();
        $this->setAdapter('Mysqli');
        $this->setParams($config->db->toArray());
        $this->_buildDatabase();
        return $this->_buildOmekaDb();
    }
    
    protected function _loadSqlFile($sqlFilename)
    {
        $sqlDir = realpath(TEST_DIR . DIRECTORY_SEPARATOR . 'Sql');
        
        $sqlPath = $sqlDir . DIRECTORY_SEPARATOR . $sqlFilename . '.sql';
        $sql = file_get_contents($sqlPath);
        // Split it on ; (better way to do this?)
        $this->_execSqlBlock($sql);
    }
    
    /**
     * Copied from Omeka_Db::execBlock()
     */
    private function _execSqlBlock($sqlBlock)
    {
        $queries = explode(';', $sqlBlock);
        foreach ($queries as $query) {
            if (strlen(trim($query))) {
                $this->getDbAdapter()->query($query);
            }
        }
    }

    private function _loadConfig()
    {   
        if (!Zend_Registry::isRegistered('test_config')) {
            //Config dependency
            $config = new Zend_Config_Ini(APP_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR.'config.ini', 'testing');
            Zend_Registry::set('test_config', $config);
        }
        
        return Zend_Registry::get('test_config');
    }
    
    private function _buildOmekaDb()
    {
        $omekaDb = new Omeka_Db($this->getDbAdapter(), 'omeka_');
        return $omekaDb;
    }
    
    private function _buildDatabase()
    {
        $this->_loadSqlFile('refresh');
    }
    
}