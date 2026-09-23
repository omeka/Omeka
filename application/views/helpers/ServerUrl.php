<?php
class Omeka_View_Helper_ServerUrl extends Zend_View_Helper_ServerUrl
{
    public function __construct()
    {
        parent::__construct();
        $serverUrlOption = get_option('server_url');
        if ($serverUrlOption) {
            $parts = parse_url($serverUrlOption);
            if (!isset($parts['scheme'], $parts['host'])) {
                throw new RuntimeException('Invalid server URL setting');
            }

            // Only set the scheme if it's https, otherwise allow auto
            if ($parts['scheme'] === 'https') {
                $this->setScheme('https');
            }

            $host = $parts['host'];
            if (isset($parts['port'])) {
                $host .= ":{$parts['port']}";
            }
            $this->setHost($host);
        }
    }
}
