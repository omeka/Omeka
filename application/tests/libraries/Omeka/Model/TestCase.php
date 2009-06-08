<?php
/**
 * Encapsulates loading and configuring access to the test database.
 *
 * @package Omeka_Testing
 * @copyright Center for History and New Media, 2009
 **/
abstract class Omeka_Model_TestCase extends PHPUnit_Framework_TestCase
{
    protected $_zendDbAdapter;
    
    public function setUp()
    {
        $this->_loadConfig();
        $this->_configDatabaseAdapter();
        $this->_buildDatabase();
        $this->_buildOmekaDb();
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
                $this->_zendDbAdapter->query($query);
            }
        }
    }
    
    private function _configDatabaseAdapter()
    {
        $config = Zend_Registry::get('test_config');

        $dbh = Zend_Db::factory('Mysqli', array(
            'host'     => $config->db->host,
            'username' => $config->db->username,
            'password' => $config->db->password,
            'dbname'   => $config->db->name
        	));
        
        $this->_zendDbAdapter = $dbh;
    }
    
    private function _loadConfig()
    {
        if (!Zend_Registry::isRegistered('test_config')) {
            //Config dependency
            $config = new Zend_Config_Ini(APP_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR.'config.ini', 'testing');
            Zend_Registry::set('test_config', $config);
        }
    }
    
    private function _buildOmekaDb()
    {
        $omekaDb = new Omeka_Db($this->_zendDbAdapter, 'omeka_');
        Omeka_Context::getInstance()->setDb($omekaDb);
    }
    
    private function _buildDatabase()
    {
        $this->_loadSqlFile('refresh');
    }
} 