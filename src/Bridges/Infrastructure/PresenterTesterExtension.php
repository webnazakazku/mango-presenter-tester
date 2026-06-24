<?php declare(strict_types = 1);

namespace Webnazakazku\MangoTester\PresenterTester\Bridges\Infrastructure;

use Nette\Application\Application;
use Nette\Application\IPresenterFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Http\IRequest;
use Nette\Http\Session;
use Nette\Routing\Router;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Security\User;
use Webnazakazku\MangoTester\Infrastructure\MangoTesterExtension;
use Webnazakazku\MangoTester\PresenterTester\IPresenterTesterListener;
use Webnazakazku\MangoTester\PresenterTester\PresenterTester;

/**
 * @property-read \stdClass $config
 */
class PresenterTesterExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'baseUrl' => Expect::string()
				->default('https://test.dev'),
			'identityFactory' => Expect::string(),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('presenterTester'))
			->setType(PresenterTester::class)
			->addSetup(new Statement(
				'?->? = ?',
				[
					$this->prefix('@presenterTesterTearDown'),
					'presenterTester',
					'@self',
				]
			));

		$builder->addDefinition($this->prefix('presenterTesterTearDown'))
			->setType(PresenterTesterTestCaseListener::class);
		$this->requireService(IPresenterFactory::class);
		$this->requireService(User::class);
		$this->requireService(Router::class);
		$this->requireService(IRequest::class);
		$this->requireService(Session::class);
		$this->requireService(Application::class);
	}

	public function beforeCompile(): void
	{
		$config = $this->config;
		$builder = $this->getContainerBuilder();
		$definition = $builder->getDefinition($this->prefix('presenterTester'));
		assert($definition instanceof ServiceDefinition);
		$definition->setArguments([
			'baseUrl' => $config->baseUrl,
			'identityFactory' => $config->identityFactory,
			'listeners' => $builder->findByType(IPresenterTesterListener::class),
		]);
	}

	/**
	 * @param class-string $class
	 */
	private function requireService(string $class): void
	{
		$builder = $this->getContainerBuilder();
		$name = (string) preg_replace('#\W+#', '_', $class);
		$builder->addDefinition($this->prefix($name))
			->setType($class)
			->addTag(MangoTesterExtension::TAG_REQUIRE);
	}

}
