<?php

use Ramsey\Uuid\Uuid;

use Lemuria\Lemuria;
use Lemuria\Model\Lemuria\Party;
use Lemuria\Test\TestConfig;

require realpath(__DIR__ . '/../vendor/autoload.php');

for ($i = 0; $i < 5; $i++) {
	echo Uuid::uuid4() . PHP_EOL;
}

$config = new TestConfig();
Lemuria::init($config);
Lemuria::load();
$registry = Lemuria::Registry();

echo $registry->count() . ' parties in registry.' . PHP_EOL;
$party = $registry->find('3a6e949b-0550-4888-becf-18e7aae2dff3');
if ($party instanceof Party) {
	echo $party->Name() . ': ' . $party->Description() . PHP_EOL;
}
