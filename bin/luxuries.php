<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\FantasyaConfig;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Tools\Lemuria\Statistics\Region\Luxuries;

require realpath(__DIR__ . '/../vendor/autoload.php');

$config = new FantasyaConfig(realpath(__DIR__ . '/../storage'));
Lemuria::init($config);
Lemuria::load();

$luxuries = new Luxuries();
$luxuries->collect(Continent::get(new Id(2))->Landmass())->getTable()->display();
