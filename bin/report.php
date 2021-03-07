<?php
declare (strict_types = 1);

use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Model\Catalog;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Test\TestConfig;
use Lemuria\Renderer\Text\HtmlWriter;
use Lemuria\Renderer\Text\OrderWriter;
use Lemuria\Renderer\Text\TextWriter;

require __DIR__ . '/../vendor/autoload.php';

$config  = new TestConfig();
$round   = $config[TestConfig::ROUND];
$reports = [];

try {
	Lemuria::init($config);
	Lemuria::Log()->debug('Report starts.', ['timestamp' => date('r')]);
	Lemuria::load();

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

	foreach (Lemuria::Catalog()->getAll(Catalog::PARTIES) as $party /* @var Party $party */) {
		$id = $party->Id();
		$name = str_replace(' ', '_', $party->Name());

		$htmlPath = $dir . DIRECTORY_SEPARATOR . $name . '.html';
		$writer   = new HtmlWriter($htmlPath);
		$writer->render($id);

		$txtPath = $dir . DIRECTORY_SEPARATOR . $name . '.txt';
		$writer  = new TextWriter($txtPath);
		$writer->render($id);

		$orderPath = $dir . DIRECTORY_SEPARATOR . $name . '.orders.txt';
		$writer = new OrderWriter($orderPath);
		$writer->render($id);

		$reports[(string)$id] = [$htmlPath, $txtPath, $orderPath];
	}

	Lemuria::Log()->debug('Report finished.', ['timestamp' => date('r')]);
} catch (Exception $e) {
	$output = (string)$e;
}
