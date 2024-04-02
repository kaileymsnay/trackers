<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2024, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kaileymsnay\trackers\controller;

/**
 * Trackers ACP controller
 */
class acp_controller
{
	/** @var \phpbb\template\template */
	protected $template;

	/** @var string */
	protected $u_action;

	/**
	 * Constructor
	 *
	 * @param \phpbb\template\template  $template
	 */
	public function __construct(\phpbb\template\template $template)
	{
		$this->template = $template;
	}

	/**
	 * Display the options a user can configure for this extension
	 */
	public function display_options()
	{
		// Create a form key for preventing CSRF attacks
		add_form_key('trackers_acp');

		// Set output variables for display in the template
		$this->template->assign_vars([
			'U_ACTION'	=> $this->u_action,
		]);
	}

	/**
	 * Set custom form action
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
