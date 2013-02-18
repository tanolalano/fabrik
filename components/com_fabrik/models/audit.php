<?php
/**
 * Fabrik Audit Model
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005 Fabrik. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
require_once 'fabrikmodelform.php';
require_once JPATH_ADMINISTRATOR . '/components/com_fabrik/tables/fabtable.php';

/**
 * Fabrik Audit Model
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @since       3.0.7
 */

class FabrikFEModelAudit extends FabModelForm
{

	public function store($formModel, $rowid, $action)
	{
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		$package = $app->getUserState('com_fabrik.package', 'fabrik');
		$orig = $session->get('com_' . $package . '.form.' . $formModel->getId() . '.data');

		$user = JFactory::getUser();
		$audit = $this->getTable();
		$data = array();
		$data['data'] = serialize($orig);
		$data['listid'] = $formModel->getListModel()->getId();
		$data['rowid'] = $rowid;
		$data['userid'] = $user->get('id');
		$data['action'] = $action;
		$audit->bind($data);
		return $audit->store();
	}

	/**
	 * Get the JTable
	 *
	 * @param   string  $name     table name
	 * @param   string  $prefix   table name prefx
	 * @param   array   $options  initial state options
	 *
	 * @return object form row
	 */

	public function getTable($name = 'Audit', $prefix = 'FabrikTable', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
	}
}
