<?php
declare(strict_types = 1);
namespace Lemuria\Game\Fantasya\Renderer\Index;

use Lemuria\Exception\LemuriaException;
use Lemuria\Renderer\Magellan\MagellanWriter;
use Lemuria\Renderer\Text\BattleLogWriter;
use Lemuria\Renderer\Text\HerbalBookWriter;
use Lemuria\Renderer\Text\HtmlWriter;
use Lemuria\Renderer\Text\OrderWriter;
use Lemuria\Renderer\Text\SpellBookWriter;
use Lemuria\Renderer\Text\TextWriter;
use Lemuria\Renderer\Text\UnicumWriter;
use Lemuria\Renderer\Writer;

enum Report : string
{
	case Battle = BattleLogWriter::class;

	case Herbs = HerbalBookWriter::class;

	case HTML = HtmlWriter::class;

	case Magellan = MagellanWriter::class;

	case Orders = OrderWriter::class;

	case Spells = SpellBookWriter::class;

	case Text = TextWriter::class;

	case Unicum = UnicumWriter::class;

	public static function fromWriter(Writer $writer): self {
		try {
			return self::from($writer::class);
		} catch (\ValueError $e) {
			throw new LemuriaException('Invalid writer class ' . $writer::class . '.', $e);
		}
	}
}
