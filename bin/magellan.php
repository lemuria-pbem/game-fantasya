<?php
declare (strict_types = 1);

use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Renderer\Magellan\MagellanWriter;
use Lemuria\Test\TestConfig;

require realpath(__DIR__ . '/../vendor/autoload.php');

try {
	Lemuria::init(new TestConfig());
	Lemuria::load();

	$path     = __DIR__ . '/../storage/turn/Name.cr';
	$magellan = new MagellanWriter($path);
	$magellan->render(Id::fromId('1'));
} catch (Throwable $e) {
	Lemuria::Log()->error('Runtime error.', ['exception' => $e]);
}
