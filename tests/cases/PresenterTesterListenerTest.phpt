<?php declare(strict_types = 1);

namespace Tests\Tester\PresenterTester;

use Tester\Assert;
use Webnazakazku\MangoTester\Infrastructure\TestCase;
use Webnazakazku\MangoTester\PresenterTester\PresenterTester;

$factory = require __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class PresenterTesterListenerTest extends TestCase
{
	public function testRender(PresenterTester $presenterTester, TestPresenterTesterListener $listener)
	{
		$listener->enabled = true;
		$request = $presenterTester->createRequest('Example');
		// action is added in listener
		$response = $presenterTester->execute($request);

		Assert::noError(function () use ($response) {
			$response->assertRenders(['Hello world']);
		});
		Assert::same($response, $listener->passedResult);
	}
}


PresenterTesterListenerTest::run($factory);
