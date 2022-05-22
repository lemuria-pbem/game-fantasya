<?php
declare(strict_types = 1);
namespace Lemuria\Alpha;

use Lemuria\Engine\Fantasya\Combat\BattleLog;
use Lemuria\Exception\LemuriaException;
use Lemuria\Model\Fantasya\Unicum;
use Lemuria\Renderer\Magellan\MagellanWriter;
use Lemuria\Renderer\PathFactory;
use Lemuria\Renderer\Text\BattleLogWriter;
use Lemuria\Renderer\Text\HtmlWriter;
use Lemuria\Renderer\Text\OrderWriter;
use Lemuria\Renderer\Text\SpellBookWriter;
use Lemuria\Renderer\Text\TextWriter;
use Lemuria\Renderer\Text\UnicumWriter;
use Lemuria\Renderer\Writer;

class AlphaPathFactory implements PathFactory
{
	protected string $name;

	public function __construct(protected string $directory) {
		if (!is_dir($this->directory)) {
			if (!mkdir($this->directory)) {
				throw new \RuntimeException('Could not create turn output directory.');
			}
			if (!chmod($this->directory, 0775)) {
				throw new \RuntimeException('Could not change access on turn output directory.');
			}
		}
	}

	public function setPrefix(string $name): void {
		$this->name = $name;
	}

	public function getPath(Writer $writer, mixed $object = null): string {
		if ($writer instanceof BattleLogWriter && $object instanceof BattleLog) {
			$fileName = $this->name . '.battle.' . $object->Location()->Id() . '.' . $object->Battle()->counter;
			return $this->directory . DIRECTORY_SEPARATOR . $fileName . '.txt';
		}
		if ($writer instanceof HtmlWriter) {
			return $this->directory . DIRECTORY_SEPARATOR . $this->name . '.html';
		}
		if ($writer instanceof MagellanWriter) {
			return $this->directory . DIRECTORY_SEPARATOR . $this->name . '.cr';
		}
		if ($writer instanceof OrderWriter) {
			return $this->directory . DIRECTORY_SEPARATOR . $this->name . '.orders.txt';
		}
		if ($writer instanceof SpellBookWriter) {
			return $this->directory . DIRECTORY_SEPARATOR . $this->name . '.spells.txt';
		}
		if ($writer instanceof TextWriter) {
			return $this->directory . DIRECTORY_SEPARATOR . $this->name . '.txt';
		}
		if ($writer instanceof UnicumWriter && $object instanceof Unicum) {
			$fileName = $this->name . '.' . $object->Composition() . '_' . $object->Id();
			return $this->directory . DIRECTORY_SEPARATOR . $fileName . '.txt';
		}
		throw new LemuriaException('Unsupported renderer: ' . $writer::class);
	}
}
