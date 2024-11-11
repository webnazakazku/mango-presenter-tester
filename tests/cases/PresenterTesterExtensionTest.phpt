<?php declare(strict_types = 1);

namespace AppTests\Tester\PresenterTester;

use ReflectionProperty;
use Tester\Assert;
use Tests\Tester\PresenterTester\TestPresenterTesterListener;
use Webnazakazku\MangoTester\Infrastructure\InfrastructureConfigurator;
use Webnazakazku\MangoTester\Infrastructure\TestCase;
use Webnazakazku\MangoTester\PresenterTester\PresenterTester;

$appConfigurator = require __DIR__ . '/../bootstrap.configurator.php';
assert($appConfigurator instanceof InfrastructureConfigurator);

/**
 * @testCase
 */
class PresenterTesterExtensionTest extends TestCase
{

	private PresenterTester $presenterTester;

	private TestPresenterTesterListener $listener;

	public function __construct(PresenterTester $presenterTester, TestPresenterTesterListener $listener)
	{
		$this->presenterTester = $presenterTester;
		$this->listener = $listener;
	}

	public function testExtension(): void
	{
		$rpBaseUrl = new ReflectionProperty(PresenterTester::class, 'baseUrl');
		$rpBaseUrl->setAccessible(true);
		Assert::same('http://mango.dev', $rpBaseUrl->getValue($this->presenterTester));

		$rpListeners = new ReflectionProperty(PresenterTester::class, 'listeners');
		$rpListeners->setAccessible(true);
		Assert::same([$this->listener], array_values($rpListeners->getValue($this->presenterTester)));
	}

}

$appConfigurator->addConfig(__DIR__ . '/extension.config.neon');
$factory = $appConfigurator->getContainerFactory();

PresenterTesterExtensionTest::run($factory);
