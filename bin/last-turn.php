<?php
declare(strict_types = 1);

use Lemuria\Game\Fantasya\FantasyaGame;

require realpath(__DIR__ . '/../vendor/autoload.php');

$fantasya = new FantasyaGame();
echo $fantasya->Round();
