<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Map\Exception;

class MissingRegionException extends ConvertException
{
	public function __construct(int $x, int $y, ?\Throwable $previous = null) {
		$message = 'The Map has no region with coordinates (' . $x . '/' . $y . ').';
		parent::__construct($message, previous: $previous);
	}
}
