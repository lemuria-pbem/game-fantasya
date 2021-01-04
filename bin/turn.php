<?php
declare (strict_types = 1);

use Lemuria\Engine\Lemuria\LemuriaTurn;
use Lemuria\Engine\Lemuria\Message\LemuriaMessage;
use Lemuria\Engine\Move\CommandFile;
use Lemuria\Exception\LemuriaException;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Exception\FileException;
use Lemuria\Model\Lemuria\Party;
use Lemuria\Test\TestConfig;

try {
	require realpath(__DIR__ . '/../vendor/autoload.php');

	try {
		Lemuria::init(new TestConfig());
		Lemuria::Log()->debug('Turn starts.', ['timestamp' => date('r')]);
		Lemuria::load();
		Lemuria::Log()->debug('Evaluating round ' . Lemuria::Calendar()->Round() . '.', ['calendar' => Lemuria::Calendar()]);
		$turn = new LemuriaTurn();
		$turn->add(new CommandFile(realpath(__DIR__ . '/../storage/orders/1.txt')));
		$turn->evaluate();
		Lemuria::Calendar()->nextRound();

		foreach (['1'] as $party) {
			$party = Party::get(Id::fromId($party));
			Lemuria::Log()->debug('Logging game messages for party ' . $party . ':');
			foreach (Lemuria::Report()->getAll($party) as $message/* @var LemuriaMessage $message */) {
				Lemuria::Log()->log($message->Level(), $message);
			}
			foreach ($party->People() as $unit) {
				foreach (Lemuria::Report()->getAll($unit) as $message/* @var LemuriaMessage $message */) {
					Lemuria::Log()->log($message->Level(), $message);
				}
			}
		}

		try {
			Lemuria::save();
			Lemuria::Log()->debug('Turn ended.');
			exit(0);
		} catch (FileException $e) {
			Lemuria::Log()->alert('Saving data failed. Turn ended.');
			exit(1);
		}
	} catch (FileException $e) {
		Lemuria::Log()->alert('Loading data failed. Turn aborted.');
		exit(2);
	}
} catch (LemuriaException $e) {
	Lemuria::Log()->emergency('Implementation error.', ['exception' => $e]);
	exit(3);
} catch (Throwable $e) {
	Lemuria::Log()->alert('Runtime error.', ['exception' => $e]);
	exit(4);
}
