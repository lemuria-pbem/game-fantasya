<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya;

use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Dispatcher\Event\Catalog\NextId;

class Monitor
{
	/**
	 * Log big IDs if this number is reached (= 10000 in base-36).
	 */
	protected const int ID_CHECK_THRESHOLD = 1679616;

	public function __construct() {
		Lemuria::Register()->addListener(new NextId(0, 0), $this->watchNextId(...));
	}

	protected function watchNextId(NextId $event): void {
		if ($event->id > self::ID_CHECK_THRESHOLD) {
			$domain = Domain::from($event->domain)->name;
			$id     = new Id($event->id);
			Lemuria::Log()->critical('A big ' . $domain . ' ID has just been created: ' . $id);
		}
	}
}
