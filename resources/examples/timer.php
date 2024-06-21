<?php
declare(strict_types = 1);

use Lemuria\Engine\Fantasya\Command\Cast\InciteMonster;
use Lemuria\Engine\Fantasya\Event\Game\BlownByTheWind;
use Lemuria\Engine\Fantasya\Event\Game\CarriedOffWayfarer;
use Lemuria\Engine\Fantasya\Event\Game\ColorOutOfSpace;
use Lemuria\Engine\Fantasya\Event\Game\CorpseFungus;
use Lemuria\Engine\Fantasya\Event\Game\Drought;
use Lemuria\Engine\Fantasya\Event\Game\FindWallet;
use Lemuria\Engine\Fantasya\Event\Game\GoblinPlague;
use Lemuria\Engine\Fantasya\Event\Game\PopulateContinent;
use Lemuria\Engine\Fantasya\Event\Game\PotionGift;
use Lemuria\Engine\Fantasya\Event\Game\Spawn;
use Lemuria\Engine\Fantasya\Event\Game\TheWildHunt;
use Lemuria\Engine\Fantasya\Event\Game\TransportMonster;
use Lemuria\Engine\Fantasya\Factory\Model\TimerEvent;
use Lemuria\Model\Fantasya\Commodity\Monster\Ent;
use Lemuria\Model\Fantasya\Commodity\Monster\Ghoul;
use Lemuria\Model\Fantasya\Commodity\Monster\Zombie;
use Lemuria\Model\Fantasya\Commodity\Potion\Brainpower;
use Lemuria\Model\Fantasya\Commodity\Silver;
use Lemuria\Model\Fantasya\Commodity\Weapon\Sword;
use Lemuria\Model\Fantasya\Race\Human;

return [
	1 => [
		new TimerEvent(GoblinPlague::class, [GoblinPlague::REGION => '', GoblinPlague::DURATION => 3]),
		new TimerEvent(CarriedOffWayfarer::class, [
			CarriedOffWayfarer::REGION    => '', CarriedOffWayfarer::RACE => Human::class,
			CarriedOffWayfarer::INVENTORY => [Silver::class => 30, Sword::class => 1]
		]),
		new TimerEvent(PotionGift::class, [PotionGift::UNIT => '', PotionGift::POTION => Brainpower::class])
	],
	3 => [
		new TimerEvent(TheWildHunt::class, [TheWildHunt::UNIT => ''])
	],
	4 => [
		new TimerEvent(Drought::class, [Drought::RATE => 0.35]),
		new TimerEvent(PopulateContinent::class, [PopulateContinent::CONTINENT => 1, PopulateContinent::CHANCES => [Ent::class => 35, Ghoul::class => 30]])
	],
	5 => [
		new TimerEvent(FindWallet::class, [FindWallet::UNIT => '', FindWallet::SILVER => 100])
	],
	6 => [
		new TimerEvent(ColorOutOfSpace::class, [ColorOutOfSpace::MOUNTAIN => '', ColorOutOfSpace::REGION => ''])
	],
	7 => [
		new TimerEvent(BlownByTheWind::class, [BlownByTheWind::REGION => '', BlownByTheWind::SPELL => InciteMonster::class]),
		new TimerEvent(Spawn::class, [Spawn::REGION => '', Spawn::SIZE => 11, Spawn::RACE => Zombie::class])
	],
	8 => [
		new TimerEvent(TransportMonster::class, [TransportMonster::UNIT => '', TransportMonster::REGION => '']),
		new TimerEvent(CorpseFungus::class, [CorpseFungus::REGION => ''])
	]
];
