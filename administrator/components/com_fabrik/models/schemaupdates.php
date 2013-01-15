<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.0.7
 * @copyright   Copyright (C) 2005 Rob Clayburn. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

require_once 'fabmodellist.php';

/**
 * Database Schema Updates Admin List Model
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.0.7
 */

class FabrikModelSchemaUpdates extends FabModelList
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see		JController
	 *
	 * @since	3.0.7
	 */

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array('id', 'filename', 'applied_by', 'applied_date', 'remote_site', 'remote_user');
		}
		parent::__construct($config);

		$new = $this->findUpdates();
		foreach ($new as $file)
		{
			$this->insertUpdate($file);
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */

	protected function getListQuery()
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', '#__{package}_schema_updates.*'));
		$query->from('#__{package}_schema_updates');

		// Join over the users for the checked out user.
		$query->select('u.name AS applied_by');
		$query->join('LEFT', '#__users AS u ON applied_by = u.id');

		// Filter by applied
		$applied = $this->getState('filter.applied');
		if (is_numeric($applied))
		{
			$query->where('applied = ' . (int) $applied);
		}
		elseif ($applied === '')
		{
			$query->where('(applied IN (0, 1, 2))');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Find new db changeset files that have been added via a git pull from a remote master site
	 *
	 * @return  array  new files to add to db
	 */

	public function findUpdates()
	{
		$files = JFolder::files(JPATH_ROOT . '/logs/dbchangeset/', '.sql');
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('filename')->from('#__{package}_schema_updates');
		$db->setQuery($query);
		$matched = $db->loadColumn();
		$new = (array_diff($files, $matched));
		return $new;
	}

	/**
	 * Insert a new db change set file into the database
	 *
	 * @param   string  $file  file name to parse and insert
	 *
	 * @return  bool
	 */

	protected function insertUpdate($file)
	{
		$row =$this->getTable();
		$log = JFile::read(JPATH_ROOT . '/logs/dbchangeset/' . $file);
		$log = explode("\n", $log);

		$row->filename = $file;
		$remoteSite = str_replace('# site:', '', $log[0]);

		if ($remoteSite === JURI::root())
		{
			return false;
		}

		$row->remote_site = $remoteSite;
		$row->remote_user = str_replace('# user:', '', $log[1]);
		return $row->store();
	}

	/**
	 * Returns an object list
	 *
	 * @param	string	The query
	 * @param	int		Offset
	 * @param	int		The number of records
	 * @return	array
	 * @since	1.5
	 */

	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$db = $this->getDbo();

		$this->_db->setQuery($query, $limitstart, $limit);
		$result = $this->_db->loadObjectList();

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('COUNT(id) AS count, group_id');
		$query->from('#__{package}_elements');
		$query->group('group_id');

		$db->setQuery($query);
		$elementcount = $db->loadObjectList('group_id');
		for ($i = 0; $i < count($result); $i++)
		{
			$k = $result[$i]->id;
			$result[$i]->_elementCount = @$elementcount[$k]->count;
		}
		return $result;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */

	public function getTable($type = 'SchemaUpdate', $prefix = 'FabrikTable', $config = array())
	{
		$config['dbo'] = FabriKWorker::getDbo();
		return FabTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 * @since	1.6
	 */

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the parameters.
		$params = JComponentHelper::getParams('com_fabrik');
		$this->setState('params', $params);

		$applied = $app->getUserStateFromRequest($this->context . '.filter.applied', 'filter_applied', '');
		$this->setState('filter.applied', $applied);

	}

	/**
	 * Run the update SQL
	 *
	 * @param   array  $ids  Ids of schema updates to run
	 *
	 * @return  int  # of schema updates successfully run
	 */

	public function run($ids)
	{
		JArrayHelper::toInteger($ids);
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$now = JFactory::getDate();
		$now = $now->toSql();
		$success = 0;
		foreach ($ids as $id)
		{
			$row = $this->getTable();
			$row->load($id);
			$log = JFile::read(JPATH_ROOT . '/logs/dbchangeset/' . $row->filename);
			$db->setQuery($log);
			if ($db->query())
			{
				$success ++;
				$update = array();
				$update['applied'] = 1;
				$update['applied_by'] = $user->get('id');
				$update['applied_date'] = $now;
				$row->bind($update);
				$row->store();
			}
			else
			{
				$update['applied'] = -1;
				$update['applied_by'] = $user->get('id');
				$update['applied_date'] = $now;
				$row->bind($update);
				$row->store();
				JError::raiseNotice(500, $db->getErrorMsg());
			}
		}
		return $success;
	}
}
