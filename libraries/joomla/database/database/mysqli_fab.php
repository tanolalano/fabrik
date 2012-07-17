<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Database
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * MySQL database driver
 *
 * @package     Joomla.Framework
 * @subpackage  Database
 * @since       1.0
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
	 * @param   string  $sql     The SQL query
	 * @param   string  $prefix  The common table prefix
	 *
	 * @return  string
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
		// Should we log the query (ingore select statements)
		$run = true;

		if ($this->noLogStrings())
		{
			$run = false;
		}

		if (is_object($this->sql))
		{
			if (!is_null($this->sql->select))
			{
				$run = false;
			}
			if ($this->isCheckout())
			{
				$run = false;
			}
		}
		else
		{

		}

		if ($run)
		{
			$user = JFactory::getUser();

			// Include the JLog class.
			jimport('joomla.log.log');
			$userName = $user->get('username');

			// Add the logger.
			$date = JFactory::getDate()->format('Y-m-d');
			/* $opts = array(
				'logger' => 'fabrik',
				'text_file' => 'dbchangeset/' . $date . '_' . microtime() . '_' . $userName . '_' . uniqid() . '.sql',
				'user' => $userName,
				'id' => uniqid()
			); */
			$opts = array(
							'logger' => 'fabrik',
							'text_file' => 'dbchangeset/' . $date . '_' . microtime() . '.sql',
							'user' => $userName,
							'id' => uniqid()
			);
			JLog::addLogger($opts, JLog::INFO, array('com_fabrik'));

			// Start logging...
			try
			{
				JLog::add($this->replacePrefix($this->sql), JLog::INFO, 'com_fabrik');
			} catch (Exception $e)
			{
				print_r($e->getMessage());exit;
			}
		}
		return parent::execute();
	}

	function noLogStrings()
	{
		if (strtoupper(JString::substr($this->sql, 0, 6)) === 'SELECT')
		{
			return true;
		}
		if (strtoupper(JString::substr($this->sql, 0, 4)) === 'SHOW')
		{
			return true;
		}
		if (strtoupper(JString::substr($this->sql, 0, 8)) === 'DESCRIBE')
		{
			return true;
		}
		if (strstr($this->sql, 'FinderIndexerAdapter'))
		{
			return true;
		}
		return false;

	}
	function isCheckout()
	{
		//

		if (is_null($this->sql->set))
		{
			return false;
		}
		//echo "<pre>";print_r($this->sql);
		$elements = $this->sql->set->getElements();
		//echo "<pre>";print_r($elements);;
		if (count($elements) !== 2)
		{
			return false;
		}
		else
		{

			if (strstr($elements[0], 'checked_out') && strstr($elements[1], 'checked_out_time'))
			{
				return true;
			}
		}
		return false;
	}
}
