<?php declare(strict_types = 1);

namespace AppTests\Tester\PresenterTester;

use Tester\Assert;
use Tester\AssertException;
use Webnazakazku\MangoTester\Infrastructure\TestCase;
use Webnazakazku\MangoTester\PresenterTester\PresenterTester;

$factory = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class PresenterTesterTest extends TestCase
{

	public function testRender(PresenterTester $presenterTester): void
	{
		$request = $presenterTester->createRequest('Example')
			->withParameters(['action' => 'render']);
		$response = $presenterTester->execute($request);

		Assert::noError(function () use ($response): void {
			$response->assertRenders(['Hello world']);
		});

		Assert::exception(function () use ($response): void {
			$response->assertRenders(['Lorem ipsum']);
		}, AssertException::class);
	}

	public function testError(PresenterTester $presenterTester): void
	{
		$request = $presenterTester->createRequest('Example')
			->withParameters(['action' => 'error']);
		$response = $presenterTester->execute($request);

		Assert::noError(function () use ($response): void {
			$response->assertBadRequest();
		});

		Assert::exception(function () use ($response): void {
			$response->assertRenders();
		}, AssertException::class);
	}

	public function testSignal(PresenterTester $presenterTester): void
	{
		$request = $presenterTester->createRequest('Example')
			->withSignal('signal', ['value' => 'abc']);
		$response = $presenterTester->execute($request);

		Assert::noError(function () use ($response): void {
			$response->assertRenders('signal processed with abc');
		});
	}

}

PresenterTesterTest::run($factory);
