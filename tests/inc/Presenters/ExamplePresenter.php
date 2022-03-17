<?php declare(strict_types = 1);

namespace Tests\Tester\PresenterTester\Presenters;

use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;

class ExamplePresenter extends Presenter
{

	public function actionRender(): void
	{
	}

	public function actionError(): void
	{
		$this->error();
	}

	/**
	 * @crossOrigin
	 */
	public function handleSignal(string $value): void
	{
		$this->sendResponse(new TextResponse('signal processed with ' . $value));
	}

}
