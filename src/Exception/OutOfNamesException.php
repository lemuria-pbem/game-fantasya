<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Exception;

use Lemuria\Exception\NamerException;

class OutOfNamesException extends NamerException
{
	public function __construct(string $domain) {
		parent::__construct('Namer is out of names for domain ' . $domain . '.');
	}
}
