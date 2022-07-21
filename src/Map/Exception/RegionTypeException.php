<?php
declare(strict_types = 1);
namespace Lemuria\Alpha\Map\Exception;

class RegionTypeException extends ConvertException
{
	public function __construct(int $x, int $y, int $type, ?\Throwable $previous = null) {
		$message = 'Region with coordinates (' . $x . '/' . $y . ') has invalid type "' . $type . '".';
		parent::__construct($message, $type, $previous);
	}
}
