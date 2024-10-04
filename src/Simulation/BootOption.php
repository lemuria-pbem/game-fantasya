<?php
declare(strict_types = 1);

namespace Lemuria\Game\Fantasya\Simulation;

enum BootOption
{
	/**
	 * Build the cache from latest Lemuria data and exit.
	 */
	case BuildCache;

	/**
	 * Delete the cache file and exit.
	 */
	case ClearCache;

	/**
	 * Try booting from cache.
	 *
	 * If booting from cache fails, start Lemuria with latest data.
	 */
	case FromCache;

	/**
	 * Boot from latest data, ignoring the cache.
	 */
	case NoCache;
}
