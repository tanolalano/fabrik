<?php
/**
 * @package     Joomla
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 Rob Clayburn. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

require_once JPATH_ADMINISTRATOR . '/components/com_fabrik/helpers/element.php';

/**
 * Reads content from a file specified by the fields value
 *
 * @package     Joomla
 * @subpackage  Form
 * @since       2.5
 */

class JFormFieldFilecontent extends JFormFieldText
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Filecontent';

	function getInput()
	{
		$content = JFile::read(JPATH_SITE . '/' . $this->element['path'] . $this->value);
		$start = "<div style=\"float:left;\">";
		$preview = "<div style=\"float:left;width:400px;height:200px;overflow:auto;border:1px solid #aaa;padding:10px;\">". nl2br($content) . "</div>";
		return $start . parent::getInput() . '<br />' . $preview . "</div>";
	}



}