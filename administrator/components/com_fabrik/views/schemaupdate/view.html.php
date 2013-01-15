<?php
/**
 * View to edit a schema update.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005 Fabrik. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @since       3.0.7
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit/apply a database schema
 *
 * @package		Joomla.Administrator
 * @subpackage	Fabrik
 * @since		3.0.7
 */
class FabrikViewSchemaUpdate extends JView
{
	/**
	 * Form
	 *
	 * @var JForm
	 */
	protected $form;

	/**
	 * Schema update item
	 *
	 * @var JTable
	 */
	protected $item;

	/**
	 * A state object
	 *
	 * @var    object
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  template
	 *
	 * @return  void
	 */

	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

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
	 * @return  void
	 */

	protected function addToolbar()
	{
		$canDo = FabrikAdminHelper::getActions();
		JRequest::setVar('hidemainmenu', true);
		JToolBarHelper::title(JText::_('COM_FABRIK_MANAGER_SCHEMA_UPDATES'), 'list.png');
		// For new records, check the create permission.
		if ($canDo->get('core.create'))
		{
			JToolBarHelper::save('schemaupdate.save', 'JTOOLBAR_SAVE');
		}
		JToolBarHelper::cancel('schemaupdate.cancel', 'JTOOLBAR_CLOSE');
	}

}
