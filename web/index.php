<?php
declare (strict_types = 1);

use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Test\TestConfig;
use Lemuria\Renderer\Text\TextWriter;
use Lemuria\Renderer\Text\HtmlWriter;

/**
 * Lemuria.
 */
require __DIR__ . '/../vendor/autoload.php';

try {
	Lemuria::init(new TestConfig());
	Lemuria::Log()->debug('Report starts.', ['timestamp' => date('r')]);
	Lemuria::load();

	$htmlPath = __DIR__ . '/../storage/save/Name.html';
	$writer   = new HtmlWriter($htmlPath);
	$writer->render(Id::fromId('1'));

	$txtPath = __DIR__ . '/../storage/save/Name.txt';
	$writer  = new TextWriter($txtPath);
	$writer->render(Id::fromId('1'));
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
		<?php if ($output): ?>
			<?= $output ?>
		<?php else: ?>
			<ul>
				<li>
					<a href="#1_html">Name (HTML)</a>
				</li>
				<li>
					<a href="#1_txt">Name (Text)</a>
				</li>
			</ul>
			<hr>
			<div id="1_html">
				<?= file_get_contents($htmlPath) ?>
			</div>
			<hr>
			<div id="1_txt">
				<pre><?= file_get_contents($txtPath) ?></pre>
			</div>
		<?php endif ?>
	</body>
</html>