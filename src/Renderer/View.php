<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Renderer;

use Lemuria\Lemuria;

class View
{
	protected ?array $variables = null;

	public function isDevelopment(): bool {
		return Lemuria::FeatureFlag()->IsDevelopment();
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
