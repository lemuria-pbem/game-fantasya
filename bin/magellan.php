<?php
declare (strict_types = 1);

use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Renderer\Magellan\MagellanWriter;
use Lemuria\Test\TestConfig;

require realpath(__DIR__ . '/../vendor/autoload.php');

$config  = new TestConfig();
$round   = $config[TestConfig::ROUND];
$parties = ['7' => 'Erben_der_Sieben', 'lem' => 'Lemurianer', 'mw' => 'Mittwaldelben'];

try {
	Lemuria::init($config);
	Lemuria::load();

	foreach ($parties as $i => $name) {
		$dir  = __DIR__ . '/../storage/turn';
		$turn = realpath($dir);
		if (!$turn) {
			throw new DirectoryNotFoundException($dir);
		}
		$dir = $turn . DIRECTORY_SEPARATOR . $round;
		if (!is_dir($dir)) {
			mkdir($dir);
			chmod($dir, 0775);
		}

		$path     = $dir . DIRECTORY_SEPARATOR . $name . '.cr';
		$magellan = new MagellanWriter($path);
		$id       = Id::fromId((string)$i);
		$magellan->render($id);
	}
} catch (Throwable $e) {
	Lemuria::Log()->error('Runtime error.', ['exception' => $e]);
}
