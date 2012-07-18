<?php
/**
 * @package Joomla
 * @subpackage Fabrik
 * @copyright Copyright (C) 2005 Rob Clayburn. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit/apply a database schema
 *
 * @package		Joomla.Administrator
 * @subpackage	Fabrik
 * @since		1.5
 */
class FabrikViewSchemaUpdate extends JView
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
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
		parent::display($tpl);
	}


}
