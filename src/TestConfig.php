<?php
declare(strict_types = 1);
namespace Lemuria\Test;

use Lemuria\Engine\Lemuria\Storage\LemuriaConfig;

class TestConfig extends LemuriaConfig
{
	public function __construct() {
		parent::__construct(__DIR__ . '/../storage');
	}
}
