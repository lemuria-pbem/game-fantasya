<?php
declare (strict_types = 1);

use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Model\Catalog;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Magellan\MagellanWriter;
use Lemuria\Test\TestConfig;

require realpath(__DIR__ . '/../vendor/autoload.php');

$config = new TestConfig();
$round  = $config[TestConfig::ROUND];

try {
	Lemuria::init($config);
	Lemuria::load();

	foreach (Lemuria::Catalog()->getAll(Catalog::PARTIES) as $party /* @var Party $party */) {
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

		$name     = str_replace(' ', '_', $party->Name());
		$path     = $dir . DIRECTORY_SEPARATOR . $name . '.cr';
		$magellan = new MagellanWriter($path);
		$magellan->render($party->Id());
	}
} catch (Throwable $e) {
	Lemuria::Log()->error('Runtime error.', ['exception' => $e]);
}
