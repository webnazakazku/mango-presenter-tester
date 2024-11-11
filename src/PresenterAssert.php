<?php declare(strict_types = 1);

namespace Webnazakazku\MangoTester\PresenterTester;

use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\StaticClass;
use Tester\Assert;
use Tester\AssertException;

class PresenterAssert
{

	use StaticClass;

	/**
	 * @param array<mixed>|null $actual
	 * @throws AssertException
	 */
	public static function assertRequestMatch(Request $expected, ?array $actual, bool $onlyIntersectedParameters = true): void
	{
		Assert::notSame(null, $actual);
		assert($actual !== null);

		$presenter = $actual[Presenter::PresenterKey] ?? null;
		Assert::same($expected->getPresenterName(), $presenter);
		unset($actual[Presenter::PresenterKey]);

		$expectedParameters = $expected->getParameters();

		foreach ($actual as $key => $actualParameter) {
			if (!isset($expectedParameters[$key])) {
				if ($onlyIntersectedParameters) {
					continue;
				}

				Assert::fail(sprintf('Parameter %s not expected', $key));
			}

			$expectedParameter = $expectedParameters[$key];
			if (is_string($actualParameter) && !is_string($expectedParameter)) {
				$expectedParameter = (string) $expectedParameter;
			}

			Assert::same($actualParameter, $expectedParameter, $key);
		}
	}

}
