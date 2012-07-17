<?php
/**
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005 Rob Clayburn. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of database schema updates.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.0.7
 */

class FabrikViewSchemaUpdates extends JView
{

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  template
	 *
	 * @return  void
	 */

	public function display($tpl = null)
	{
		// Initialise variables.
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->formOptions = $this->get('FormOptions');
		$this->packageOptions = $this->get('PackageOptions');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	3.0.7
	 *
	 * @return  void
	 */

	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/fabrik.php';
		$canDo = FabrikHelper::getActions();

		JToolBarHelper::title(JText::_('COM_FABRIK_MANAGER_SCHEMA_UPDATES'), 'list.png');

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_fabrik');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_FABRIK_SCHEMA_UPDATES', false, JText::_('JHELP_COMPONENTS_FABRIK_SCHEMA_UPDATES'));
	}
}
