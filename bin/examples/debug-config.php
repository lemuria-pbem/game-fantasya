<?php
declare(strict_types = 1);

use Lemuria\Engine\Fantasya\Turn\SelectiveCherryPicker;

function configure(SelectiveCherryPicker $cherryPicker): void {
	$cherryPicker->everyone()->everything();
}
