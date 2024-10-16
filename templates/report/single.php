<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\Renderer\Index\Report;
use Lemuria\Game\Fantasya\Renderer\View;
use Lemuria\Model\Fantasya\Party;

/** @var View $this */

/** @var Party $party  */
$party = $this->variables[0];
/** @var Report $report */
$report = $this->variables[1];

$target = $report->name . '-' . $party->Id() . '-' . $this->navigation->Round();

?>
<?php if ($this->report($report)): ?>
	<a class="d-xl-none" href="<?= $this->path() ?>" target="<?= $target ?>"><?= $report->name[0] ?></a>
	<a class="d-none d-xl-inline-block" href="<?= $this->path() ?>" target="<?= $target ?>"><?= $report->name ?></a>
<?php else: ?>
	<span class="no-report"></span>
<?php endif ?>
