<?php declare(strict_types = 1);

namespace Webnazakazku\MangoTester\PresenterTester;

interface IPresenterTesterListener
{

	public function onRequest(TestPresenterRequest $request): TestPresenterRequest;

	public function onResult(TestPresenterResult $result): void;

}
