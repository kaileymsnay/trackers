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
 * Trackers ACP module info
 */
class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\kaileymsnay\trackers\acp\main_module',
			'title'		=> 'ACP_TRACKERS_TITLE',
			'modes'		=> [
				'settings'	=> [
					'title'	=> 'ACP_TRACKERS',
					'auth'	=> 'ext_kaileymsnay/trackers && acl_a_trackers_manage',
					'cat'	=> ['ACP_TRACKERS_TITLE'],
				],
			],
		];
	}
}
