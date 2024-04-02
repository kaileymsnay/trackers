<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2024, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kaileymsnay\trackers\includes;

use Symfony\Component\DependencyInjection\ContainerInterface;

class functions
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var array */
	protected $tables;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface                    $container
	 * @param \phpbb\auth\auth                      $auth
	 * @param \phpbb\db\driver\driver_interface     $db
	 * @param \phpbb\request\request                $request
	 * @param \phpbb\template\template              $template
	 * @param array                                 $tables
	 */
	public function __construct(ContainerInterface $container, \phpbb\auth\auth $auth, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template, $tables)
	{
		$this->container = $container;
		$this->auth = $auth;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->tables = $tables;

		$this->project = $this->container->get('kaileymsnay.trackers.project');
	}

	/**
	 * Returns whether the current user can report/see private tickets
	 */
	public function can_report_private()
	{
		if ($this->auth->acl_get('a_') || $this->auth->acl_getf_global('m_'))
		{
			return true;
		}

		if ($this->project->is_team_user())
		{
			return true;
		}

		return false;
	}

	/**
	 * Returns whether the current user is able to set the severity of a ticket
	 */
	public function can_set_severity()
	{
		if ($this->project->is_team_user() || $this->auth->acl_getf_global('m_'))
		{
			return true;
		}

		return false;
	}

	/**
	 * Get array of duplicates of ticket $ticket_id
	 */
	public function get_duplicate_tickets($ticket_id)
	{
		$tickets = [];

		$sql = 'SELECT t.ticket_id, t.ticket_title
			FROM ' . $this->tables['trackers_tickets'] . ' t
			LEFT JOIN ' . $this->tables['trackers_status'] . ' st
				ON (st.status_id = t.status_id)
			WHERE st.ticket_duplicate = 1
				AND t.duplicate_id = ' . (int) $ticket_id;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$id = (int) $row['ticket_id'];
			$tickets[$id] = $row;
		}
		$this->db->sql_freeresult($result);

		return $tickets;
	}

	/**
	 * Naturally sort an array by its key, keeping the key => data association
	 */
	public function knatsort(&$array_input)
	{
		if (!is_array($array_input))
		{
			return false;
		}

		$array_keys = $array_output = [];

		foreach ($array_input as $key => $value)
		{
			$array_keys[] = $key;
		}

		if (!@natsort($array_keys))
		{
			return false;
		}

		foreach ($array_keys as $key => $value)
		{
			$array_output[$value] = $array_input[$value];
		}

		$array_input = $array_output;

		return true;
	}

	/**
	 * Return "(unassigned)" if the input is empty
	 */
	public function unassigned_check($input, $index = '', $array_field = '')
	{
		if (is_array($input))
		{
			if (!isset($input[$index]))
			{
				return '(' . strtolower($this->language->lang('UNASSIGNED')) . ')';
			}
			else if (is_array($input[$index]) && isset($input[$index][$array_field]))
			{
				return $input[$index][$array_field];
			}
			else
			{
				return $input[$index];
			}
		}

		return (empty($input)) ? '(' . strtolower($this->language->lang('UNASSIGNED')) . ')' : $input;
	}

	public function generate_navlinks($navlinks)
	{
		if ($navlinks)
		{
			foreach ($navlinks as $navlink)
			{
				$this->template->assign_block_vars('navlinks', [
					'FORUM_NAME'	=> $navlink['FORUM_NAME'],
					'U_VIEW_FORUM'	=> $navlink['U_VIEW_FORUM'],
				]);
			}
		}
	}
}
