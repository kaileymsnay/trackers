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

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Trackers main controller
 */
class main_controller
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\language\language */
	protected $language;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface        $container
	 * @param \phpbb\language\language  $language
	 */
	public function __construct(ContainerInterface $container, \phpbb\language\language $language)
	{
		$this->container = $container;
		$this->language = $language;
	}

	/**
	 * Controller handler for route /trackers/{page}
	 */
	public function display($page)
	{
		switch ($page)
		{
			case 'viewproject':
				return $this->container->get('kaileymsnay.trackers.viewproject')->display();
			break;

			case 'viewticket':
				return $this->container->get('kaileymsnay.trackers.viewticket')->display();
			break;

			case 'viewtracker':
				return $this->container->get('kaileymsnay.trackers.viewtracker')->display();
			break;

			default:
				throw new \phpbb\exception\http_exception(404, $this->language->lang('NO_PAGE_MODE'));
			break;
		}
	}
}
