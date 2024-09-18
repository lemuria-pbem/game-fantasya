<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Factory;

use Lemuria\Engine\Fantasya\Factory\Namer\DefaultNamer;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Exception\FileException;
use Lemuria\Exception\NamerException;
use Lemuria\Game\Fantasya\Exception\OutOfNamesException;
use Lemuria\Identifiable;
use Lemuria\Lemuria;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Factory\BuilderTrait;
use Lemuria\Model\Fantasya\Navigable;
use Lemuria\Model\Fantasya\Region;

class FantasyaNamer extends DefaultNamer
{
	use BuilderTrait;

	protected const string STORAGE = __DIR__ . '/../../storage/names';

	/**
	 * @var array<string, array<string>>
	 */
	protected ?array $names = null;

	/**
	 * @var array<string, int>
	 */
	protected array $count = [];

	public function __destruct() {
		$this->updateNameLists();
	}

	/**
	 * @throws NamerException
	 */
	public function name(Domain|Identifiable $entity): string {
		if ($this->names === null) {
			$this->loadNameLists();
		}
		return parent::name($entity);
	}

	public function updateNameLists(): void {
		if ($this->names) {
			$dir = realpath(self::STORAGE);
			if (!$dir) {
				throw new DirectoryNotFoundException(self::STORAGE);
			}
			foreach ($this->names as $domain => $names) {
				$n = count($names);
				if ($n < $this->count[$domain]) {
					$path = $dir . DIRECTORY_SEPARATOR . $domain . '.lst';
					if (file_put_contents($path, implode(PHP_EOL, array_reverse($names))) === false) {
						throw new FileException('Could not update ' . $domain . ' names file.');
					}
					Lemuria::Log()->debug('Names file for ' . $domain . ' has been updated with ' . $n . ' lines.');
				}
			}
			$this->names = null;
		}
	}

	protected function location(Region $region): string {
		$landscape = $region->Landscape();
		if ($landscape instanceof Navigable) {
			return $this->translateSingleton($landscape);
		}
		return $this->next(__FUNCTION__);
	}

	/**
	 * @throws OutOfNamesException
	 */
	protected function next(string $domain): string {
		if (!isset($this->names[$domain]) || empty($this->names[$domain])) {
			throw new OutOfNamesException($domain);
		}
		return array_pop($this->names[$domain]);
	}

	protected function loadNameLists(): void {
		$dir = realpath(self::STORAGE);
		if (!$dir) {
			throw new DirectoryNotFoundException(self::STORAGE);
		}

		Lemuria::Log()->debug('Loading name files...');
		$this->names = [];
		foreach (glob($dir . DIRECTORY_SEPARATOR . '*.lst') as $path) {
			$fileName = basename($path);
			$domain   = substr($fileName, 0, strlen($fileName) - 4);
			$names    = [];
			$file     = fopen($path, 'r');
			if ($file) {
				while (!feof($file)) {
					$line = fgets($file);
					if ($line) {
						$line = trim($line);
						if ($line) {
							$names[] = $line;
						}
					}
				}
			}
			$this->names[$domain] = array_reverse($names);
			$n                    = count($names);
			$this->count[$domain] = $n;
			Lemuria::Log()->debug('Names file for ' . $domain . ' has been loaded (' . $n . ' names).');
		}
	}
}
