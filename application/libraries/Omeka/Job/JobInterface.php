<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Interface for jobs.
 * 
 * @package Omeka\Job
 */
interface Omeka_Job_JobInterface
{
    public function __construct(array $options);
    public function perform();
}
