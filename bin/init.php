<?php
declare(strict_types = 1);

use Lemuria\Test\TestConfig;

require __DIR__ . '/../vendor/autoload.php';

$config = new TestConfig();
?>
Game config initialized.
Current round: <?= $config[TestConfig::ROUND] ?>

