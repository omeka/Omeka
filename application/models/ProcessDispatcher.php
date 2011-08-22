<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 */
 
/**
 * Spawns and manages background processes.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ProcessDispatcher
{
    /**
     * Create a table entry for a new background process and spawn it.
     *
     * @param string $className ProcessAbstract subclass name to spawn
     * @param User $user User to run process as, defaults to current user
     * @param Array|null $args Arguments specific to the child class process
     * @param string $lastPhase The last phase to load during the phased loading process (see Omeka_Core) 
     * @return Process The model object for the background process
     */
    static public function startProcess($className, $user = null, $args = null, $lastPhase = 'initializeRoutes')
    {
        $cliPath = self::getPHPCliPath();
                
        if (!$user) {
            $user = Omeka_Context::getInstance()->getCurrentUser();
        }
        
        $process = new Process;
        $process->class = $className;
        $process->user_id = $user->id;
        $process->status = Process::STATUS_STARTING;
        $process->setArguments($args);
        $process->started = date('Y-m-d G:i:s');
        $process->stopped = '0000-00-00 00:00:00';
        $process->save();
        
        $command = escapeshellcmd($cliPath) . ' '
                 . escapeshellarg(self::_getBootstrapFilePath())
                 . " -p " . escapeshellarg($process->id)
                 . " -l " . escapeshellarg($lastPhase);
        self::_fork($command);
        
        return $process;
    }

    /**
     * Stops a background process in progress.
     *
     * @param Process $process The process to stop.
     * @return bool True if the process was stopped, false if not.
     */
    static public function stopProcess(Process $process)
    {
        if ($process->status == Process::STATUS_STARTING ||
            $process->status == Process::STATUS_IN_PROGRESS) {
            $pid = $process->pid;
            $process->stopped = date('Y-m-d G:i:s');
            if ($pid) {
                $command = "kill "
                         . escapeshellarg($pid);
                exec($command);
            }
            // Consider a "STOPPED" status instead.
            $process->status = Process::STATUS_STOPPED;
            $process->pid = null;
            $process->save();
            return true;
        } else {
            return false;
        }
    }
    
    static public function getPHPCliPath()
    {
        // Use the user-specified path, or attempt autodetection if no path
        // specified.
        $cliPath = Omeka_Context::getInstance()->getConfig('basic')->background->php->path;
        
        if ($cliPath == "") {
            $cliPath = self::_autodetectCliPath();
        }
        
        self::_checkCliPath($cliPath);
        
        return $cliPath;
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
    
    static private function _autodetectCliPath()
    {
        $command = 'which php 2>&0';
        $lastLineOutput = exec($command, $output, $returnVar);
        return $returnVar == 0 ? $lastLineOutput : '';
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
    static private function _fork($command) 
    {
        exec("$command > /dev/null 2>&1 &");
    }
}
