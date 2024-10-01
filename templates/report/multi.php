<?php
declare (strict_types = 1);

use Lemuria\Game\Fantasya\Renderer\View;

/** @var View $this */

$reports = $this->variables[0];

$links = [];
foreach ($reports as $fileName):
	$links[] = "<a href='" . $fileName . "' target='" . substr($fileName, 0, strpos($fileName, '.')) . "'>" . $fileName . "</a>";
endforeach

?>
<?= implode('<br>', $links) ?>
