<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Renderer\Index;

use Lemuria\Model\Fantasya\Party;
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

	public function register(Party $party): static {
		$this->party[$party->Id()->Id()] = new Reports();
		$this->current                   = $party;
		return $this;
	}

	/**
	 * @todo Replace with event listener.
	 */
	public function add(Writer $writer, mixed $object = null): static {
		$reports = $this->party[$this->current->Id()->Id()];
		$path    = $this->pathFactory->getPath($writer, $object);
		return $this;
	}
}
