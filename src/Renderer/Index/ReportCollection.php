<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Renderer\Index;

use Lemuria\Exception\LemuriaException;
use Lemuria\Id;
use Lemuria\Model\Fantasya\Gathering;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Renderer\PathFactory;
use Lemuria\Renderer\Writer;

class ReportCollection
{
	/**
	 * @var array<int, Reports>
	 */
	protected array $party = [];

	protected Party $current;

	public function __construct(protected readonly PathFactory $pathFactory) {
	}

	public function Parties(): Gathering {
		$parties  = new Gathering();
		$noOrders = [];
		$npcs     = [];
		$monsters = [];

		foreach ($this->party as $id => $reports) {
			$party = Party::get(new Id($id));
			switch ($party->Type()) {
				case Type::NPC :
					$npcs[] = $party;
					break;
				case Type::Monster :
					$monsters[] = $party;
					break;
				default :
					if ($reports->Received()) {
						$parties->add($party);
					} else {
						$noOrders[] = $party;
					}
			}
		}

		foreach ($npcs as $party) {
			$parties->add($party);
		}
		foreach ($monsters as $party) {
			$parties->add($party);
		}
		foreach ($noOrders as $party) {
			$parties->add($party);
		}
		return $parties;
	}

	public function getReports(Party $party): Reports {
		$id = $party->Id()->Id();
		if (isset($this->party[$id])) {
			return $this->party[$id];
		}
		throw new LemuriaException('Party ' . $party . ' is not registered in collection.');
	}

	public function register(Party $party, ?int $received): static {
		$this->party[$party->Id()->Id()] = new Reports($received);
		$this->current                   = $party;
		return $this;
	}

	/**
	 * @todo Replace with event listener.
	 */
	public function add(Writer $writer, mixed $object = null): static {
		$reports = $this->party[$this->current->Id()->Id()];
		$reports->add(Report::fromWriter($writer), basename($this->pathFactory->getPath($writer, $object)));
		return $this;
	}
}
