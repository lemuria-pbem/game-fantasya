<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Event;

use Lemuria\Engine\Fantasya\Event\Timer;
use Lemuria\Engine\Fantasya\Factory\Model\TimerEvent;
use Lemuria\Exception\FileNotFoundException;
use Lemuria\Exception\LemuriaException;

final readonly class TimerInjector
{
	public function __construct(private string $resourcePath) {
		if (!is_file($resourcePath)) {
			throw new FileNotFoundException($resourcePath);
		}
	}

	public function inject(Timer $timer): void {
		$resource = @include $this->resourcePath;
		if (!is_array($resource)) {
			throw new LemuriaException('Error in timer resources file.');
		}
		foreach ($resource as $round => $events) {
			if (!is_int($round) || !is_array($events)) {
				throw new LemuriaException('Invalid round or events in timer resources file.');
			}
			foreach ($events as $event) {
				if ($event instanceof TimerEvent) {
					$timer->add($round, $event);
				} else {
					throw new LemuriaException('Expected only timer events for round ' . $round . '.');
				}
			}
		}
	}
}
