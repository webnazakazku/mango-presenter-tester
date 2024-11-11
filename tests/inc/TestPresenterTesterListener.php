<?php declare(strict_types = 1);

namespace Tests\Tester\PresenterTester;

use Webnazakazku\MangoTester\PresenterTester\IPresenterTesterListener;
use Webnazakazku\MangoTester\PresenterTester\TestPresenterRequest;
use Webnazakazku\MangoTester\PresenterTester\TestPresenterResult;

class TestPresenterTesterListener implements IPresenterTesterListener
{

	public bool $enabled = false;

	public ?TestPresenterResult $passedResult = null;

	public function onRequest(TestPresenterRequest $request): TestPresenterRequest
	{
		if (!$this->enabled) {
			return $request;
		}

		return $request->withParameters(['action' => 'render']);
	}

	public function onResult(TestPresenterResult $result): void
	{
		$this->passedResult = $result;
	}

}
