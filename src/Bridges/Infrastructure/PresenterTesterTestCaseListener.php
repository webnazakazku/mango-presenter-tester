<?php declare(strict_types = 1);

namespace Webnazakazku\MangoTester\PresenterTester\Bridges\Infrastructure;

use Webnazakazku\MangoTester\Infrastructure\ITestCaseListener;
use Webnazakazku\MangoTester\Infrastructure\TestCase;
use Webnazakazku\MangoTester\PresenterTester\PresenterTester;
use Tester\Assert;


class PresenterTesterTestCaseListener implements ITestCaseListener
{
	/** @var PresenterTester|NULL */
	public $presenterTester;


	public function setUp(TestCase $testCase): void
	{
	}


	public function tearDown(TestCase $testCase): void
	{
		if (!$this->presenterTester) {
			return;
		}
		foreach ($this->presenterTester->getResults() as $i => $result) {
			if (!$result->wasResponseInspected()) {
				Assert::fail(sprintf('Request #%d to %s presenter was not asserted', $i + 1, $result->getRequest()->getPresenterName()));
			}
		}
	}
}
