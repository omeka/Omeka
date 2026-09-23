<?php
/**
 * Set default value for the server_url setting
 *
 * @package Omeka\Db\Migration
 */
class setServerUrl extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $serverUrlHelper = new Zend_View_Helper_ServerUrl;
        $validator = new Omeka_Validate_ServerUrl;
        $validator->setDisableTranslator(true);
        $serverUrl = $serverUrlHelper->serverUrl();
        if ($validator->isValid($serverUrl)) {
            set_option('server_url', $serverUrl);
        }
    }
}
