<?php
declare(strict_types = 1);

use Lemuria\Game\Fantasya\FantasyaConfig;
use Lemuria\Game\Fantasya\FantasyaPathFactory;
use Lemuria\Exception\DirectoryNotFoundException;
use Lemuria\Game\Fantasya\Renderer\Magellan\FantasyaHeader;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Magellan\WorldInspector;
use Lemuria\Tools\Lemuria\Scenario\FocusFinder;

require realpath(__DIR__ . '/../vendor/autoload.php');

$storage = realpath(__DIR__ . '/../storage');
if (!$storage) {
	throw new DirectoryNotFoundException($storage);
}

$config = new FantasyaConfig($storage);
Lemuria::init($config->setLogFile('npc-map.log'));
Lemuria::Log()->debug('Loading Lemuria.', ['storage' => $storage]);

try {
	Lemuria::load();

	$header = new FantasyaHeader();
	$party  = Party::get(Id::fromId('n'));
	$focus  = new FocusFinder();
	$focus->setParty($party);
	$regions = $focus->Landmass();

	$mapFile   = $storage . '/turn/npc.cr';
	$inspector = new WorldInspector(new FantasyaPathFactory($storage));
	$inspector->setHeader($header)->setParty($party)->setRegions($regions)->withInfrastructure()->setPath($mapFile)->render(new Id(0));
	Lemuria::Log()->debug('Magellan map saved to ' . realpath($mapFile) . '.');
} catch (\Throwable $e) {
	Lemuria::Log()->emergency($e->getMessage(), ['exception' => $e]);
}
