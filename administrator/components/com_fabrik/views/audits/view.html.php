<?php
/**
 *  View class for a list's history/audit.
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005 Fabrik. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of lists.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.0.7
 */
class FabrikViewAudits extends JView
{
	/**
	 * List items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * Pagination
	 *
	 * @var  JPagination
	 */
	protected $pagination;

	/**
	 * View state
	 *
	 * @var object
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   strin  $tpl  Template name
	 *
	 * @return void
	 */

	public function display($tpl = null)
	{
		// Initialise variables.
		$model = $this->getModel();
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->userOptions = $model->getUserOptions();
		$this->listOptions = $model->getListOptions();
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
		require_once JPATH_COMPONENT . '/helpers/fabrik.php';
		$canDo = FabrikAdminHelper::getActions($this->state->get('filter.category_id'));

		if ($canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'audits.delete', 'JTOOLBAR_DELETE');
		}
		JToolBarHelper::title(JText::_('COM_FABRIK_MANAGER_AUDITS'), 'lists.png');
		JToolBarHelper::custom('audits.rollback', 'extension.png', 'extension_f2.png', 'COM_FABRIK_ROLLBACK', false);
		JToolBarHelper::help('JHELP_COMPONENTS_FABRIK_AUDIT', false, JText::_('JHELP_COMPONENTS_FABRIK_AUDIT'));
	}

}
