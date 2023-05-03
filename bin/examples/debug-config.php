<?php
declare(strict_types = 1);

use Lemuria\Engine\Fantasya\Turn\SelectiveCherryPicker;

function configure(SelectiveCherryPicker $cherryPicker): void {
	$cherryPicker->everyone()->everything();
}

function modifyAnythingForDebugging(): void {
	// Modify anything here that is needed for debugging.
}
