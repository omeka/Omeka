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
 * @package Omeka\Job\Process
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Job_Process_Dispatcher
{
    /**
     * Create a table entry for a new background process and spawn it.
     *
     * @param string $className Omeka_Job_Process_AbstractProcess subclass name 
     * to spawn
     * @param User $user User to run process as, defaults to current user
     * @param Array|null $args Arguments specific to the child class process
     * @return Process The model object for the background process
     */
    public static function startProcess($className, $user = null, $args = null)
    {
        $cliPath = self::getPHPCliPath();

        if (!$user) {
            $user = Zend_Registry::get('bootstrap')->getResource('CurrentUser');
        }

        if ($user) {
            $user_id = $user->id;
        } else {
            $user_id = 0;
        }

        $process = new Process;
        $process->class = $className;
        $process->user_id = $user_id;
        $process->status = Process::STATUS_STARTING;
        $process->setArguments($args);
        $process->started = date('Y-m-d G:i:s');
        $process->save();

        $command = escapeshellarg($cliPath) . ' '
                 . escapeshellarg(self::_getBootstrapFilePath())
                 . " -p " . escapeshellarg($process->id);
        self::_fork($command);

        return $process;
    }

    /**
     * Stops a background process in progress.
     *
     * @param Process $process The process to stop.
     * @return bool True if the process was stopped, false if not.
     */
    public static function stopProcess(Process $process)
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

    public static function getPHPCliPath()
    {
        // Use the user-specified path, or attempt autodetection if no path
        // specified.
        $cliPath = Zend_Registry::get('bootstrap')->getResource('Config')->background->php->path;

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
    private static function _checkCliPath($cliPath)
    {
        /**
         * All of this could be moved, or also used, when actually setting the
         * php_cli_path option
         */
        // Try to execute PHP and check for appropriate version
        $command = escapeshellarg($cliPath) . ' -v';
        $output = array();
        exec($command, $output, $returnCode);

        if ($returnCode != 0) {
            throw new RuntimeException(__('The configured PHP path (%s) is invalid.', $cliPath));
        }

        // Attempt to parse the output from 'php -v' (the first line only)
        preg_match('/(^\\w+) ([\\d\\.]+)/', $output[0], $matches);
        $cliName = $matches[1];
        $cliVersion = $matches[2];
        $phpVersion = phpversion();

        if ($cliName != 'PHP' || !$cliVersion) {
            throw new RuntimeException(__('The configured PHP path (%s) does not point to a PHP-CLI binary.', $cliPath));
        } elseif (version_compare($cliVersion, '5.2', '<')) {
            throw new RuntimeException(__('The configured PHP path (%s) points to a PHP-CLI binary with an invalid version (%s)', $cliPath, $cliVersion));
        } elseif ($cliVersion != $phpVersion) {
            // potentially display a warning for this
        }
        return true;
    }

    private static function _autodetectCliPath()
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
    private static function _getBootstrapFilePath()
    {
        return SCRIPTS_DIR . '/background.php';
    }

    /**
     * Launch a background process, returning control to the foreground.
     *
     * @link http://www.php.net/manual/en/ref.exec.php#70135
     */
    private static function _fork($command)
    {
        exec("$command > /dev/null 2>&1 &");
    }
}
