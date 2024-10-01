<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Renderer\Index;

class Reports
{
	protected array $reports = [
		Report::HTML->name     => null,
		Report::Text->name     => null,
		Report::Orders->name   => null,
		Report::Magellan->name => null,
		Report::Herbs->name    => null,
		Report::Spells->name   => null,
		Report::Battle->name   => [],
		Report::Unicum->name   => []
	];

	public function __construct(protected ?int $received) {
	}

	public function HTML(): ?string {
		return $this->reports[Report::HTML->name];
	}

	public function Text(): ?string {
		return $this->reports[Report::Text->name];
	}

	public function Orders(): ?string {
		return $this->reports[Report::Orders->name];
	}

	public function Magellan(): ?string {
		return $this->reports[Report::Magellan->name];
	}

	public function Herbs(): ?string {
		return $this->reports[Report::Herbs->name];
	}

	public function Spells(): ?string {
		return $this->reports[Report::Spells->name];
	}

	public function Battle(): array {
		return $this->reports[Report::Battle->name];
	}

	public function Unicum(): array {
		return $this->reports[Report::Unicum->name];
	}

	public function Received(): ?int {
		return $this->received;
	}

	public function getReport(Report $report): array|string|null {
		return $this->reports[$report->name];
	}

	public function add(Report $report, string $fileName): static {
		if (is_array($this->reports[$report->name])) {
			$this->reports[$report->name][] = $fileName;
		} else {
			$this->reports[$report->name] = $fileName;
		}
		return $this;
	}
}
