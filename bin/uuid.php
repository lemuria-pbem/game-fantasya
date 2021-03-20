<?php
declare(strict_types = 1);

use Ramsey\Uuid\Uuid;

require realpath(__DIR__ . '/../vendor/autoload.php');

echo Uuid::uuid4() . PHP_EOL;
