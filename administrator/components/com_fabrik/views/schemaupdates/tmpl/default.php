<?php
/**
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005 Rob Clayburn. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/helpers/adminhtml.php';
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHTML::_('script', 'system/multiselect.js', false, true);
$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

$alts = array('JPUBLISHED', 'JUNPUBLISHED', 'COM_FABRIK_ERR_CHANGESET_NOT_APPLIED');
$imgs = array('publish_x.png', 'tick.png', 'publish_y.png');
$tasks = array('run', '', 'run');
$appliedState = $this->state->get('filter.applied');

?>
<form action="<?php echo JRoute::_('index.php?option=com_fabrik&view=schemaupdates'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">

		<div class="filter-select fltrt">

			<select name="filter_applied" class="inputbox" onchange="this.form.submit()">
				<option <?php echo $appliedState == 0 ? 'selected="selected"' : '' ?>value="0">Not applied</option>
				<option <?php echo $appliedState == 1 ? 'selected="selected"' : '' ?>value="1">Applied</option>
				<option <?php echo $appliedState == 2 ? 'selected="selected"' : '' ?>value="2">Failed</option>
				<option <?php echo $appliedState == '' ? 'selected="selected"' : '' ?>value=""><?php echo JText::_('COM_FABRIK_ALL')?></option>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="2%">
					<?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
				</th>
				<th></th>
				<th width="30%" >
					<?php echo JHTML::_('grid.sort', 'COM_FABRIK_FILE', 'filename', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHTML::_('grid.sort', 'COM_FABRIK_APPLIED', 'applied', $listDirn, $listOrder); ?>
				</th>
				<th width="13%" >
					<?php echo JHTML::_('grid.sort', 'COM_FABRIK_APPLED_BY', 'applied_by', $listDirn, $listOrder); ?>
				</th>
				<th width="15%">
					<?php echo JHTML::_('grid.sort', 'COM_FABRIK_APPLIED_DATE', 'applied_date', $listDirn, $listOrder); ?>
				</th>
				<th width="15%">
					<?php echo JHTML::_('grid.sort', 'COM_FABRIK_REMOTE_SITE', 'remote_site', $listDirn, $listOrder); ?>
				</th>
				<th width="15%">
				<?php echo JHTML::_('grid.sort', 'COM_FABRIK_REMOTE_USER', 'remote_user', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
	$ordering = ($listOrder == 'ordering');
	$link = JRoute::_('index.php?option=com_fabrik&task=schemaupdate.edit&id=' . (int) $item->id);
			   ?>

			<tr class="row<?php echo $i % 2; ?>">
					<td>
						<?php echo $item->id; ?>
					</td>
					<td>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>">
							<?php echo $item->filename; ?>
						</a>
					</td>
					<td>
						<?php
						$canChange = $item->applied == 1 ? false : true;
						echo FabrikHelperAdminHTML::multistate(array(0, 1, 2), $i, $item->applied, $tasks, $imgs, $alts, 'schemaupdates.', $canChange); ?>
					</td>
					<td>
						<?php echo $item->applied_by; ?>
					</td>
					<td>
						<?php echo $item->applied_date ?>
					</td>
					<td>
						<?php echo $item->remote_site; ?>
					</td>
					<td>
						<?php echo $item->remote_user ; ?>
					</td>
				</tr>

			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
