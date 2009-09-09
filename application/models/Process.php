<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 */
 
/**
 * Base class background processes descend from.
 */
class Process extends Omeka_Record
{
    const STATUS_STARTING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_PAUSED = 4;
    const STATUS_ERROR = 5;
    
    public $pid;
    public $class;
    public $user_id;
    public $status;
}