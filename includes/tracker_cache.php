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

class tracker_cache
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var array */
	protected $tables;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface                    $container
	 * @param \phpbb\cache\driver\driver_interface  $cache
	 * @param \phpbb\db\driver\driver_interface     $db
	 * @param \phpbb\language\language              $language
	 * @param array                                 $tables
	 */
	public function __construct(ContainerInterface $container, \phpbb\cache\driver\driver_interface $cache, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, $tables)
	{
		$this->container = $container;
		$this->cache = $cache;
		$this->db = $db;
		$this->language = $language;
		$this->tables = $tables;
	}

	/**
	 * Get tracker data
	 */
	public function get_tracker_data($tracker_id)
	{
		$trackers = $this->cache->get('_trackers');

		if (!$trackers)
		{
			$sql = 'SELECT *
				FROM ' . $this->tables['trackers_trackers'];
			$result = $this->db->sql_query($sql);
			$trackers = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$trackers[$row['tracker_id']] = $row;
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_trackers', $trackers);
		}

		if (!isset($trackers[$tracker_id]))
		{
			return false;
		}

		return $trackers[$tracker_id];
	}

	/**
	 * Get project data
	 */
	public function get_project_data($project_id)
	{
		$projects = $this->cache->get('_trackers_projects');

		if (!$projects)
		{
			$sql = 'SELECT *
				FROM ' . $this->tables['trackers_projects'];
			$result = $this->db->sql_query($sql);
			$projects = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$projects[$row['project_id']] = $row;
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_trackers_projects', $projects);
		}

		if (!isset($projects[$project_id]))
		{
			return false;
		}

		return $projects[$project_id];
	}

	/**
	 * Get the available projects
	 */
	public function get_projects()
	{
		$projects = $this->cache->get('_trackers_projects');

		if (!$projects)
		{
			$sql = 'SELECT *
				FROM ' . $this->tables['trackers_projects'];
			$result = $this->db->sql_query($sql);
			$projects = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$projects[$row['project_id']] = $row;
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_trackers_projects', $projects);
		}

		return $projects;
	}

	/**
	 * Destroy the cached projects
	 */
	public function destroy_projects_cache()
	{
		$this->cache->destroy('_trackers_projects');
	}

	/**
	 * Destroy the cached data for a specific project
	 */
	public function destroy_project_cache($project_id)
	{
		$projects = $this->cache->get('_trackers_projects');

		if ($projects && isset($projects[$project_id]))
		{
			unset($projects[$project_id]);
			$this->cache->put('_trackers_projects', $projects);
		}
	}

	/**
	 * Get available tracker statuses
	 */
	public function get_statuses($tracker_id)
	{
		$statuses = $this->cache->get('_trackers_statuses');

		if (!$statuses || !isset($statuses[$tracker_id]))
		{
			$sql = 'SELECT *
				FROM ' . $this->tables['trackers_status'] . '
				WHERE tracker_id = ' . (int) $tracker_id . '
				ORDER BY tracker_id, status_order';
			$result = $this->db->sql_query($sql);
			$statuses = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$statuses[$row['tracker_id']][$row['status_id']] = $row;
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_trackers_statuses', $statuses);
		}

		if (!isset($statuses[$tracker_id]))
		{
			return [];
		}

		return $statuses[$tracker_id];
	}

	/**
	 * Get available tracker severities
	 */
	public function get_severities($tracker_id)
	{
		$severities = $this->cache->get('_trackers_severities');

		if (!$severities || !isset($severities[$tracker_id]))
		{
			$severities = [];

			$severities[$tracker_id][0] = [
				'tracker_id'		=> (int) $tracker_id,
				'severity_id'		=> 0,
				'severity_name'		=> $this->language->lang('UNCATEGORISED'),
				'severity_colour'	=> '',
				'severity_order'	=> -1,
			];

			$sql = 'SELECT tracker_id, severity_id, severity_name, severity_colour
				FROM ' . $this->tables['trackers_severity'] . '
				WHERE tracker_id = ' . (int) $tracker_id . '
				ORDER BY tracker_id, severity_order';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$severities[$row['tracker_id']][$row['severity_id']] = $row;
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_trackers_severities', $severities);
		}

		if (!isset($severities[$tracker_id]))
		{
			return [];
		}

		return $severities[$tracker_id];
	}

	/**
	 * Destroy the cached statuses
	 */
	public function destroy_statuses_cache($project_id)
	{
		$statuses = $this->cache->get('_trackers_statuses');

		if ($statuses && isset($statuses[$project_id]))
		{
			unset($statuses[$project_id]);
			$this->cache->put('_trackers_statuses', $statuses);
		}
	}

	/**
	 * Get available project components
	 */
	public function get_components($project_id)
	{
		$components = $this->cache->get('_trackers_components');

		if (!$components || !isset($components[$project_id]))
		{
			$sql = 'SELECT project_id, component_id, component_name
				FROM ' . $this->tables['trackers_components'] . '
				WHERE project_id = ' . (int) $project_id . '
				ORDER BY project_id, component_name';
			$result = $this->db->sql_query($sql);
			$components = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$components[$row['project_id']][$row['component_id']] = $row['component_name'];
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_trackers_components', $components);
		}

		if (!isset($components[$project_id]))
		{
			return [];
		}

		return $components[$project_id];
	}

	/**
	 * Destroy the cached components
	 */
	public function destroy_components_cache($project_id)
	{
		$components = $this->cache->get('_trackers_components');

		if ($components && isset($components[$project_id]))
		{
			unset($components[$project_id]);
			$this->cache->put('_trackers_components', $components);
		}
	}

	/**
	 * Get the group IDs and names of the groups that can be assigned tickets
	 */
	public function get_team_groups($project_id)
	{
		$groups = $this->cache->get('_trackers_team_groups');

		if (!$groups || !isset($groups[$project_id]))
		{
			$sql = 'SELECT g.group_id, g.group_name
				FROM ' . $this->tables['trackers_project_auth'] . ' a, ' . $this->tables['groups'] . ' g
				WHERE a.project_id = ' . (int) $project_id . '
					AND a.group_id = g.group_id
				ORDER BY g.group_name';
			$result = $this->db->sql_query($sql);
			$groups = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$groups[$project_id][$row['group_id']] = $row['group_name'];
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_trackers_team_groups', $groups);
		}

		if (!isset($groups[$project_id]))
		{
			return [];
		}

		return $groups[$project_id];
	}

	/**
	 * Delete the cached team groups
	 */
	public function destroy_team_groups_cache($project_id)
	{
		$groups = $this->cache->get('_trackers_team_groups');

		if ($groups && isset($groups[$project_id]))
		{
			unset($groups[$project_id]);
			$this->cache->put('_trackers_team_groups', $groups);
		}
	}

	/**
	 * Get the user IDs and usernames of the users that can be assigned tickets
	 */
	public function get_team_users($project_id)
	{
		$users = $this->cache->get('_trackers_team_users');

		if (!$users || !isset($users[$project_id]))
		{
			$users = $auth_groups = $users_unsorted = [];

			$sql = 'SELECT u.user_id, u.username
				FROM ' . $this->tables['trackers_project_auth'] . ' a, ' . $this->tables['users'] . ' u
				WHERE u.user_id = a.user_id
					AND a.project_id = ' . (int) $project_id;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$users_unsorted[$row['user_id']] = $row['username'];
			}
			$this->db->sql_freeresult($result);

			$sql = 'SELECT group_id
				FROM ' . $this->tables['trackers_project_auth'] . '
				WHERE project_id = ' . (int) $project_id;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$auth_groups[] = $row['group_id'];
			}
			$this->db->sql_freeresult($result);

			if (count($auth_groups) > 0)
			{
				$sql = 'SELECT u.user_id, u.username
					FROM ' . $this->tables['users'] . ' u, ' . $this->tables['user_group'] . ' ug
					WHERE u.user_id = ug.user_id
						AND ug.user_pending = 0
						AND ' . $this->db->sql_in_set('ug.group_id', $auth_groups) . '
					GROUP BY u.user_id';
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$users_unsorted[$row['user_id']] = $row['username'];
				}
				$this->db->sql_freeresult($result);
			}

			// Sort the usernames and cache them
			natcasesort($users_unsorted);

			foreach ($users_unsorted as $user_id => $user_name)
			{
				$users[$project_id][$user_id] = $user_name;
			}

			$this->cache->put('_trackers_team_users', $users, 3600);
		}

		if (!isset($users[$project_id]))
		{
			return [];
		}

		return $users[$project_id];
	}

	/**
	 * Delete the cached team users
	 */
	public function destroy_team_users_cache($project_id)
	{
		$users = $this->cache->get('_trackers_team_users');

		if ($users && isset($users[$project_id]))
		{
			unset($users[$project_id]);
			$this->cache->put('_trackers_team_users', $users);
		}
	}
}
