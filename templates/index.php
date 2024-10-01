<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\Renderer\Index\Report;
use Lemuria\Game\Fantasya\Renderer\View;

/** @var View $this */

$tabIndex = 0;

?>
<body id="administrator-index">
	<h1>Auswertungsübersicht</h1>

	<p>erstellt am <?= date('d.m.Y H:i') . ' Uhr' ?></p>

	<div class="container-fluid">
		<?php foreach($this->collection->Parties() as $party): ?>
			<div class="row bg-<?= $this->has($party)->status() ?>-subtle">
				<div class="col-6 col-sm-8 col-lg-2 col-xl-3 party">
					<?= $party->Name() ?>
					<span class="badge text-bg-primary font-monospace"><?= $party->Id() ?></span>
				</div>
				<div class="col-6 col-sm-4 col-lg-2 col-xl-1"><?= $this->when() ?></div>
				<div class="col-1">
					<?= $this->template('report/single', $party, Report::HTML) ?>
				</div>
				<div class="col-1">
					<?= $this->template('report/single', $party, Report::Text) ?>
				</div>
				<div class="col-1">
					<?= $this->template('report/single', $party, Report::Magellan) ?>
				</div>
				<div class="col-1">
					<?= $this->template('report/single', $party, Report::Orders) ?>
				</div>
				<div class="col-1">
					<?= $this->template('report/single', $party, Report::Spells) ?>
				</div>
				<div class="col-1">
					<?= $this->template('report/single', $party, Report::Herbs) ?>
				</div>
				<div class="col-2 col-md-1">
					<?php if ($reports = $this->multiReports(Report::Battle)): ?>
						<a tabindex="<?= $tabIndex++ ?>" role="button" data-bs-toggle="popover" data-bs-trigger="focus"
						   data-bs-title="<?= $this->number(count($reports), 'Kampf', 'Kämpfe') ?>"
						   data-bs-content="<?= $this->template('report/multi', $reports) ?>"
						>
							<span class="d-xl-none" title="<?= $this->number(count($reports), 'Kampf', 'Kämpfe') ?>">⚔️&nbsp;<?= count($reports) ?></span>
							<span class="d-none d-xl-inline-block"><?= $this->number(count($reports), 'Kampf', 'Kämpfe') ?></span>
						</a>
					<?php else: ?>
						<span class="no-reports" title="(keine Kämpfe)">⚔️</span>
					<?php endif ?>
				</div>
				<div class="col-2 col-md-1">
					<?php if ($reports = $this->multiReports(Report::Unicum)): ?>
						<a tabindex="<?= $tabIndex++ ?>" role="button" data-bs-toggle="popover" data-bs-trigger="focus"
						   data-bs-title="<?= $this->number(count($reports), 'Unikat', 'Unikate') ?>"
						   data-bs-content="<?= $this->template('report/multi', $reports) ?>"
						>
							<span class="d-xl-none" title="<?= $this->number(count($reports), 'Unikat', 'Unikate') ?>">🗏&nbsp;<?= count($reports) ?></span>
							<span class="d-none d-xl-inline-block"><?= $this->number(count($reports), 'Unikat', 'Unikate') ?></span>
						</a>
					<?php else: ?>
						<span class="no-reports" title="(keine Unikate)">🗏️</span>
					<?php endif ?>
				</div>
			</div>
		<?php endforeach ?>
	</div>
</body>
