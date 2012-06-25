<?php
/**
 * @version		$Id: mysql.php 18554 2010-08-21 03:19:19Z ian $
 * @package		Joomla.Framework
 * @subpackage	Database
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * MySQL database driver
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.0
 */
class JDatabaseMySQLi_Fab extends JDatabaseMySQLi
{
	/**
	 * The database driver name
	 *
	 * @var string
	 */
	public $name = 'mysqli_fab';

	/**
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>_table_prefix</var> class variable.
	 *
	 * @param	string	The SQL query
	 * @param	string	The common table prefix
	 */
	public function replacePrefix($sql, $prefix='#__')
	{
		$app = JFactory::getApplication();
		$package = $app->getUserStateFromRequest('com_fabrik.package', 'package', 'fabrik');
		$sql = str_replace('{package}', $package, $sql);
		return parent::replacePrefix($sql, $prefix);
	}

	
	public function execute()
	{
		//should we log the query (ingore select statements)
		$run = true;
		if (is_object($this->sql))
		{
			if (!is_null($this->sql->select))
			{
				$run = false;
			}
		}
		else
		{
			if (strtoupper(JString::substr($this->sql, 0, 6)) === 'SELECT'
			|| strtoupper(JString::substr($this->sql, 0, 4)) === 'SHOW'
			)
			{
				$run = false;
			}
		}
		if ($run)
		{
			$user = JFactory::getUser();
			// Include the JLog class.
			jimport('joomla.log.log');
			$userName = $user->get('username');
			// Add the logger.
			$date = JFactory::getDate()->format('Y-m-d');
			$opts = array(
				'logger' => 'liquibase', 
				'text_file' => 'dbchangeset/' . $date . '_' . $userName . '_' . uniqid() . '.sql',
				'user' => $userName,
				'id' => uniqid()
			);
			JLog::addLogger($opts, JLog::INFO, array('com_fabrik'));
			
			// start logging...
			JLog::add($this->replacePrefix($this->sql), JLog::INFO, 'com_fabrik');
		}
		return parent::execute();
	}
	
}
