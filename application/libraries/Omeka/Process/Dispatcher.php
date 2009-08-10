<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * Spawns and manages background processes.
 * @package Omeka
 */
class Omeka_Process_Dispatcher
{
    /**
     * Create a table entry for a new background process and spawn it.
     *
     * @param string $className Omeka_Process_Abstract subclass name to spawn
     * @param User $user User to run process as, defaults to current user
     * @return Process The model object for the background process
     */
    static public function startProcess($className, $user = null)
    {
        $cliPath = get_option('php_cli_path');
        
        self::_checkCliPath($cliPath);
        
        if (!$user) {
            $user = Omeka_Context::getInstance()->getCurrentUser();
        }
        
        $process = new Process;
        $process->class = $className;
        $process->user_id = $user->id;
        $process->status = Process::STATUS_STARTING;
        $process->save();
        
        $command = escapeshellcmd($cliPath).' '
                 . self::_getBootstrapFilePath()
                 . " -p $process->id";
        self::_fork($command);
        
        return $process;
    }
    
    static public function stopProcess(Process $process)
    {
        
    }
    
    /**
     * Checks if the configured PHP-CLI path points to a valid PHP binary.
     * Flash an appropriate error if the path is invalid.
     */
    static private function _checkCliPath($cliPath)
    {
        /**
         * All of this could be moved, or also used, when actually setting the
         * php_cli_path option
         */
        // Try to execute PHP and check for appropriate version
        $command = escapeshellcmd($cliPath).' -v';
        $output = array();
        exec($command, $output, $returnCode);
        
        $error = "The configured PHP path ($cliPath)";
        if ($returnCode != 0) {
            throw new Exception($error.' is invalid.');
        }
        
        // Attempt to parse the output from 'php -v' (the first line only)
        preg_match('/(^\\w+) ([\\d\\.]+)/', $output[0], $matches);
        $cliName    = $matches[1];
        $cliVersion = $matches[2];
        $phpVersion = phpversion();
        
        if ($cliName != 'PHP'  || !$cliVersion) {
            throw new Exception($error.' does not point to a PHP-CLI binary.');
        } else if (version_compare($cliVersion, '5.2', '<')) {
            throw new Exception($error.' points to a PHP-CLI binary with an'
                                      ." invalid version ($cliVersion)");
        } else if ($cliVersion != $phpVersion) {
            // potentially display a warning for this
        }
        return true;
    }
    
    
    /**
     * Returns the path to the background bootstrap script.
     *
     * @return string Path to bootstrap
     */
    static private function _getBootstrapFilePath()
    {
        return BACKGROUND_BOOTSTRAP_PATH;
    }
    
    /**
     * Launch a background process, returning control to the foreground.
     * 
     * @link http://www.php.net/manual/en/ref.exec.php#70135
     */
    static private function _fork($command) {
        exec("$command > /dev/null 2>&1");
    }
}