<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Renderer;

use Lemuria\Game\Fantasya\Renderer\Index\Report;
use Lemuria\Game\Fantasya\Renderer\Index\ReportCollection;
use Lemuria\Game\Fantasya\Renderer\Index\Reports;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Party\Type;

class View
{
	protected ReportCollection $collection;

	protected ?array $variables = null;

	protected Party $party;

	private Reports $current;

	private array|string|null $report;

	public function isDevelopment(): bool {
		return Lemuria::FeatureFlag()->IsDevelopment();
	}

	public function setReportCollection(ReportCollection $collection): void {
		$this->collection = $collection;
	}

	/**
	 * Generate the template output.
	 */
	public function generate(): string {
		if (!ob_start()) {
			throw new \RuntimeException('Could not start output buffering.');
		}
		return $this->generateContent('index');
	}

	public function template(string $name, mixed ...$variables): string {
		$this->variables = $variables;
		return $this->generateContent($name);
	}

	public function has(Party $party): static {
		$this->party   = $party;
		$this->current = $this->collection->getReports($party);
		return $this;
	}

	public function received(): bool {
		return (bool)$this->current->Received();
	}

	public function status(): string {
		return match ($this->party->Type()) {
			Type::NPC     => 'success',
			Type::Monster => 'primary',
			default       => $this->received() ? 'light' : 'warning'
		};
	}

	public function when(): string {
		$timestamp = $this->current->Received();
		return $timestamp ? \DateTimeImmutable::createFromFormat('U', (string)$timestamp)->format('d.m.y H:i') : '';
	}

	public function report(Report $report): bool {
		$this->report = $this->current->getReport($report);
		return !empty($this->report);
	}

	public function multiReports(Report $report): ?array {
		$reports = $this->current->getReport($report);
		return is_array($reports) ? $reports : null;
	}

	public function path(): array|string {
		return $this->report;
	}

	public function number(int $count, string $singular, string $plural): string {
		return match($count) {
			0       => '(keine ' . $plural . ')',
			1       => '1 ' . $singular,
			default => $count . ' ' . $plural
		};
	}

	protected function generateContent(string $template): string {
		ob_start();
		$result = @include __DIR__ . '/../../templates/' . $template . '.php';
		$output = ob_get_clean();
		if ($result) {
			return $output;
		}
		throw new \RuntimeException('Template error.' . ($output ? PHP_EOL . $output : ''));
	}
}
