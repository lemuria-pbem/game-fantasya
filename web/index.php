<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\LemuriaAlpha;

require __DIR__ . '/../vendor/autoload.php';

$lemuriaAlpha = new LemuriaAlpha();

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
		<?php foreach ($lemuriaAlpha->getReports() as $path): ?>
			<hr>
			<div>
				<?= file_get_contents($path) ?>
			</div>
		<?php endforeach ?>
	</body>
</html>