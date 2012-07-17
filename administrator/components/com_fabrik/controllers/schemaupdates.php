<?php
/**
* @package     Joomla
* @subpackage  Fabrik
* @copyright   Copyright (C) 2005 Rob Clayburn. All rights reserved.
* @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

// No direct access.
defined('_JEXEC') or die;

require_once 'fabcontrolleradmin.php';

/**
 * Schema Update List Controller Class
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.0.7
 */

class FabrikControllerSchemaUpdates extends FabControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	3.0.7
	 */
	protected $text_prefix = 'COM_FABRIK_SCHEMA_UPDATES';

	protected $view_item = 'schemaupdates';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see		JController
	 *
	 * @since	1.6
	 */

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    model name
	 * @param   string  $prefix  model prefix
	 *
	 * @since	3.0.7
	 *
	 * @return  object  models
	 */

	public function &getModel($name = 'SchemaUpdates', $prefix = 'FabrikModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

}
