<?php

use Ramsey\Uuid\Uuid;

require_once __DIR__ . '/../vendor/autoload.php';

for ($i = 0; $i < 5; $i++) {
	echo Uuid::uuid4() . PHP_EOL;
}
