<?php declare(strict_types = 1);

use Webnazakazku\MangoTester\Infrastructure\InfrastructureConfigurator;

require __DIR__ . '/../vendor/autoload.php';

DG\BypassFinals::enable();

$configurator = new InfrastructureConfigurator(__DIR__ . '/temp');
$configurator->addConfig(__DIR__ . '/config/infrastructure.neon');
$configurator->setupTester();
$configurator->addParameters(['configDir' => __DIR__ . '/config']);

return $configurator;
