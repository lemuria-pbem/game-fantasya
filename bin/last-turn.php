<?php
declare(strict_types = 1);

use Lemuria\Game\Fantasya\LemuriaAlpha;

require realpath(__DIR__ . '/../vendor/autoload.php');

$lemuriaAlpha = new LemuriaAlpha();
echo $lemuriaAlpha->Round();
