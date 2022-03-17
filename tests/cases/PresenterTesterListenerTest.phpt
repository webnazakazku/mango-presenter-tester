<?php declare(strict_types = 1);

namespace AppTests\Tester\PresenterTester;

use Tester\Assert;
use Tests\Tester\PresenterTester\TestPresenterTesterListener;
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
