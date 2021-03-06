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

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="de" xmlns="http://www.w3.org/1999/html">
	<head>
		<title>Lemuria-Auswertung</title>
		<link rel="stylesheet" href="/css/bootstrap.css"/>
		<link rel="stylesheet" href="/css/style.css"/>
		<script type="text/javascript" src="/js/jquery.min.js"></script>
		<script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="/js/script.js"></script>
	</head>
	<body>
		<?php if (isset($output)): ?>
			<?= $output ?>
		<?php else: ?>
			<?php foreach (Lemuria::Catalog()->getAll(Catalog::PARTIES) as $party): ?>
				<ul>
					<li>
						<a href="#<?= $party->Id() ?>_html"><?= $party ?> (HTML)</a>
					</li>
					<li>
						<a href="#<?= $party->Id() ?>_txt"><?= $party ?> (Text)</a>
					</li>
					<li>
						<a href="#<?= $party->Id() ?>_orders"><?= $party ?> (Zugvorlage)</a>
					</li>
				</ul>
			<?php endforeach ?>
			<?php foreach ($reports as $id => $files): ?>
				<hr>
				<div id="<?= $id ?>_html">
					<?= file_get_contents($files[0]) ?>
				</div>
				<hr>
				<div id="<?= $id ?>_txt">
					<pre><?= file_get_contents($files[1]) ?></pre>
				</div>
				<hr>
				<div id="<?= $id ?>_orders">
					<pre><?= file_get_contents($files[2]) ?></pre>
				</div>
			<?php endforeach ?>
		<?php endif ?>
	</body>
</html>