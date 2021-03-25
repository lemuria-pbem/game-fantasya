<?php
declare(strict_types = 1);
namespace Lemuria\Alpha;

use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;

final class AlphaConfig extends LemuriaConfig
{
	public const DEBUG_PARTIES = 'debugParties';

	private const DEBUG_PARTIES_DEFAULT = [];

	protected function initDefaults(): void {
		parent::initDefaults();
		$this->defaults[self::DEBUG_PARTIES] = self::DEBUG_PARTIES_DEFAULT;
	}
}
