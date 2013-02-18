<?php
/**
 * Fabrik Admin Audits Model
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005 Fabrik. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @since       1.6
 */

defined('_JEXEC') or die;

require_once 'fabmodellist.php';

/**
 * Fabrik Admin Audits Model
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.0
 */

class FabrikModelAudits extends FabModelList
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see		JController
	 * @since	1.6
	 */

	public function __construct($config = array())
	{

		parent::__construct($config);
	}

	/**
	 * Delete audits
	 *
	 * @param   array  $ids  Audit ids
	 *
	 * @return  bool
	 */

	public function delete($ids)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__{package}_audit')->where('id IN (' . implode(',', $ids) . ')');
		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */

	protected function getListQuery()
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*, l.label AS list, u.name as user'));
		$query->from('#__{package}_audit AS a');

		// Join to get list name
		$query->join('LEFT', '#__{package}_lists AS l ON l.id = a.listid');
		$query->join('LEFT', '#__users AS u ON u.id = a.userid');

		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(l.db_table_name LIKE ' . $search . ' OR l.label LIKE ' . $search . ')');
		}

		$listid = $this->getState('filter.listid');
		if (!empty($listid))
		{
			$query->where('l.id = ' . (int) $listid);
		}

		$userid = $this->getState('filter.userid');
		if (!empty($userid))
		{
			$query->where('u.id = ' . (int) $userid);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * User filter list options
	 *
	 * @return  array
	 */

	public function getUserOptions()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT userid AS value, u.name AS text')->from('#__{package}_audit AS a')
		->join('LEFT', '#__users AS u ON u.id = a.userid');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Get list filter dropdown options
	 *
	 * @return  array
	 */

	public function getListOptions()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT listid AS value, l.label AS text')->from('#__{package}_audit AS a')
		->join('LEFT', '#__{package}_lists AS l ON l.id = a.listid');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */

	public function getItems()
	{
		$items = parent::getItems();

		// Get unique listid/rowids
		$listModels = array();
		foreach ($items as &$item)
		{
			if (!array_key_exists($item->listid, $listModels))
			{
				$listModel = JModel::getInstance('List', 'FabrikFEModel');
				$listModel->setId($item->listid);
				$listModels[$item->listid] = $listModel;
			}
			else
			{
				$listModel = $listModels[$item->listid];
			}
			$formModel = $listModel->getFormModel();

			$audit = unserialize($item->data);
			$formModel->setRowId($item->rowid);

			$current = $formModel->getData();

			$diff = FArrayHelper::array_diff_nested($current, $audit);
			$this->formatDiff($item, $diff, 'diff');

			$curr = FArrayHelper::array_diff_nested($audit, $current);
			$this->formatDiff($item, $curr, 'curr');

		}
		return $items;
	}

	/**
	 * Format the audit difference data
	 *
	 * @param   object  &$item    Audit item
	 * @param   array   $diff     Array differnce between current audit item and current record
	 * @param   string  $diffKey  Key to assign formatted diff text to $item.
	 *
	 * @return  void
	 */
	protected function formatDiff(&$item, $diff, $diffKey)
	{
		$ignore = array('view', 'layout', 'Itemid', 'formid', 'rowid', 'listid');
		$diff = array_diff($diff, $ignore);
		foreach ($ignore as $i)
		{
			unset($diff[$i]);
		}
		if (empty($diff))
		{
			$item->$diffKey = '';
		}
		else
		{
			$item->$diffKey = '<ul>';
			foreach ($diff as $k => $v)
			{
				$item->$diffKey .= '<li>' . $k . ': ' . $v . '</li>';
			}
			$item->$diffKey .= '</ul>';
		}
	}

	/**
	 * Rollback a record to a previous state. If successful removed audit from list
	 *
	 * @param   array  $ids  Audit ids to roll back from
	 *
	 * @return  bool
	 */
	public function rollBack($ids)
	{
		JArrayHelper::toInteger($ids);
		$listModels = array();
		foreach ($ids as $id)
		{
			$table = $this->getTable();
			$table->load($id);
			if (!array_key_exists($table->listid, $listModels))
			{
				$listModel = JModel::getInstance('List', 'FabrikFEModel');
				$listModel->setId($table->listid);
				$listModels[$table->listid] = $listModel;
			}
			else
			{
				$listModel = $listModels[$table->listid];
			}
			$formModel = $listModel->getFormModel();

			$data = unserialize($table->data);
			$formModel->_formData = $data;
			$formModel->setRowId($table->rowid);

			// Set repeat join totals
			if (array_key_exists('join', $data))
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true);
				$query->select('id, group_id')->from('#__{package}_joins')->where('id IN (' . implode(',', array_keys($data['join'])) . ')');
				$db->setQuery($query);
				$groupIds = $db->loadObjectList('id');
				foreach ($data['join'] as $joinId => $joinData)
				{
					$groupid = $groupIds[$joinId]->group_id;

					$repeatTotals[$groupid] = count($joinData[array_keys($joinData)[0]]);
				}
			}
			JRequest::setVar('fabrik_repeat_group', $repeatTotals);

			$formModel->rollBack = true;
			if ($formModel->process())
			{
				$item = $this->getTable();
				$item->load($id);
				$item->delete();
			}
			else
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable	A database object
	 */

	public function getTable($type = 'Audit', $prefix = 'FabrikTable', $config = array())
	{
		$config['dbo'] = FabriKWorker::getDbo();
		return FabTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the parameters.
		$params = JComponentHelper::getParams('com_fabrik');
		$this->setState('params', $params);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the list filter state
		$list = $app->getUserStateFromRequest($this->context . '.filter.listid', 'filter_listid', '');
		$this->setState('filter.listid', $list);

		// Load the user filter state
		$user = $app->getUserStateFromRequest($this->context . '.filter.userid', 'filter_userid', '');
		$this->setState('filter.userid', $user);

		// List state information.
		parent::populateState('label', 'asc');
	}
}
