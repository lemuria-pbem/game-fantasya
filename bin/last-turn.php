<?php
declare(strict_types = 1);

use Lemuria\Alpha\LemuriaAlpha;

require realpath(__DIR__ . '/../vendor/autoload.php');

$lemuriaAlpha = new LemuriaAlpha();
echo $lemuriaAlpha->Round();
