<?php
declare (strict_types = 1);

use Lemuria\Alpha\LemuriaAlpha;

require realpath(__DIR__ . '/../vendor/autoload.php');

$lemuriaAlpha = new LemuriaAlpha();

$lemuriaAlpha->init()->readOrders()->evaluate()->finish()->createReports()->createArchives();
