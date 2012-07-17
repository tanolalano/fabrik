<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.log.logger');

// Register the JLoggerFormattedText class with the autoloader.
JLoader::register('JLoggerFabrik', dirname(__FILE__) . '/fabrik.php');

/**
 * Joomla! Fabrik logger
 *
 * This class is designed to build db crud changes which can then be accessed by Fabrik
 *
 * @package     Joomla.Platform
 * @subpackage  Log
 * @since       11.1
 */
class JLoggerFabrik extends JLoggerFormattedText
{
	/**
	 * @var    string  The format which each entry follows in the log file.  All fields must be
	 * named in all caps and be within curly brackets eg. {FOOBAR}.
	 * @since  11.1
	 */
	protected $format = "{MESSAGE};";

	public function __construct(array &$options)
	{
		parent::__construct($options);
		$this->options['text_file_no_php'];
	}

	protected function generateFileHeader()
	{
		$user = JFactory::getUser();
		$config = JFactory::getConfig();
		return "# site:" . JURI::root() . "\n" .
		"# user:" . $user->get('username') . "\n";
	}
}
