<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Map\Exception;

class ConvertException extends \RuntimeException
{
	public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}
