<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2024, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kaileymsnay\trackers\acp;

/**
 * Trackers ACP module
 */
class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	/**
	 * Main ACP module
	 */
	public function main($id, $mode)
	{
		global $phpbb_container;

		/** @var \kaileymsnay\trackers\controller\acp_controller $acp_controller */
		$acp_controller = $phpbb_container->get('kaileymsnay.trackers.controller.acp');

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'acp_trackers_body';

		// Set the page title for our ACP page
		$this->page_title = 'ACP_TRACKERS_TITLE';

		// Make the $u_action url available in our ACP controller
		$acp_controller->set_page_url($this->u_action);

		// Load the display options handle in our ACP controller
		$acp_controller->display_options();
	}
}
