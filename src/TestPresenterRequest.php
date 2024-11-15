<?php declare(strict_types = 1);

namespace Webnazakazku\MangoTester\PresenterTester;

use Nette\Forms\Controls\CsrfProtection;
use Nette\Http\Session;
use Nette\Security\IIdentity;
use Nette\SmartObject;
use Nextras\Application\UI\Helpers as NextrasSecuredHelpers;

/**
 * Immutable object
 */
class TestPresenterRequest
{

	use SmartObject;

	private string $methodName = 'GET';

	/** @var array<mixed> */
	private array $headers = [];

	private string $presenterName;

	/** @var array<mixed> */
	private array $parameters = [];

	/** @var array<mixed> */
	private array $post = [];

	/** @var string|NULL */
	private ?string $rawBody = null;

	/** @var array<mixed> */
	private array $files = [];

	private bool $ajax = false;

	/** @var string|NULL */
	private ?string $componentClass = null;

	private bool $shouldHaveIdentity = false;

	/** @var IIdentity|NULL */
	private ?IIdentity $identity = null;

	private Session $session;

	public function __construct(string $presenterName, Session $session)
	{
		if ($session instanceof \Webnazakazku\MangoTester\HttpMocks\Session) {
			$session->setFakeId('mango.id');
		}

		$session->getSection(CsrfProtection::class)->token = 'mango.token';
		$this->presenterName = $presenterName;
		$this->session = $session;
	}

	public function getMethodName(): string
	{
		return $this->methodName;
	}

	/**
	 * @return mixed[]
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

	public function getPresenterName(): string
	{
		return $this->presenterName;
	}

	/**
	 * @return mixed[]|string[]
	 */
	public function getParameters(): array
	{
		return $this->parameters + ['action' => 'default'];
	}

	/**
	 * @return mixed[]
	 */
	public function getPost(): array
	{
		return $this->post;
	}

	public function getRawBody(): ?string
	{
		return $this->rawBody;
	}

	/**
	 * @return mixed[]
	 */
	public function getFiles(): array
	{
		return $this->files;
	}

	public function isAjax(): bool
	{
		return $this->ajax;
	}

	public function getComponentClass(): ?string
	{
		return $this->componentClass;
	}

	public function shouldHaveIdentity(): bool
	{
		return $this->shouldHaveIdentity;
	}

	public function getIdentity(): ?IIdentity
	{
		return $this->identity;
	}

	/**
	 * @param array<mixed> $componentParameters
	 * @param string|NULL $componentClass required for a secured signal
	 */
	public function withSignal(string $signal, array $componentParameters = [], ?string $componentClass = null): TestPresenterRequest
	{
		assert(!isset($this->parameters['do']));
		$request = clone $this;
		$request->componentClass = $componentClass;
		$request->parameters['do'] = $signal;
		$lastDashPosition = strrpos($signal, '-');
		$componentName = $lastDashPosition !== false ? substr($signal, 0, $lastDashPosition) : '';

		if ($componentClass && class_exists(NextrasSecuredHelpers::class)) {
			$csrfToken = NextrasSecuredHelpers::getCsrfToken(
				$this->session,
				$componentClass,
				'handle' . lcfirst(substr($signal, $lastDashPosition ? $lastDashPosition + 1 : 0)),
				[
					$componentName, array_map(fn ($param) => is_object($param) && method_exists($param, 'getId') ? $param->getId() : $param, $componentParameters)]
			);
			$componentParameters['_sec'] = $csrfToken;
		}

		if ($componentName !== '') {
			$newParameters = [];
			foreach ($componentParameters as $key => $value) {
				$newParameters[$componentName . '-' . $key] = $value;
			}

			$componentParameters = $newParameters;
		}

		$request->parameters = $componentParameters + $request->parameters;

		return $request;
	}

	public function withMethod(string $methodName): TestPresenterRequest
	{
		$request = clone $this;
		$request->methodName = $methodName;

		return $request;
	}

	/**
	 * @param array<mixed> $post
	 * @param array<mixed> $files
	 */
	public function withForm(string $formName, array $post, array $files = [], bool $withProtection = true): TestPresenterRequest
	{
		$request = $this->withSignal($formName . '-submit');
		if ($withProtection) {
			$token = 'abcdefghij' . base64_encode(sha1(('mango.token' ^ $this->session->getId()) . 'abcdefghij', true));
			$post += ['_token_' => $token];
		}

		$request->post = $post;
		$request->files = $files;

		return $request;
	}

	public function withRawBody(string $rawBody): TestPresenterRequest
	{
		$request = clone $this;
		$request->rawBody = $rawBody;

		return $request;
	}

	/**
	 * @param array<mixed> $headers
	 */
	public function withHeaders(array $headers): TestPresenterRequest
	{
		$request = clone $this;
		$request->headers = array_change_key_case($headers, CASE_LOWER) + $request->headers;

		return $request;
	}

	public function withAjax(bool $enable = true): TestPresenterRequest
	{
		$request = clone $this;
		$request->ajax = $enable;

		return $request;
	}

	/**
	 * @param array<mixed> $parameters
	 */
	public function withParameters(array $parameters): TestPresenterRequest
	{
		$request = clone $this;
		$request->parameters = $parameters + $this->parameters;

		return $request;
	}

	/**
	 * @param array<mixed> $post
	 */
	public function withPost(array $post): TestPresenterRequest
	{
		$request = clone $this;
		$request->post = $post + $this->post;

		return $request;
	}

	/**
	 * @param array<mixed> $files
	 */
	public function withFiles(array $files): TestPresenterRequest
	{
		$request = clone $this;
		$request->files = $files + $this->files;

		return $request;
	}

	public function withIdentity(?IIdentity $identity = null): TestPresenterRequest
	{
		$request = clone $this;
		$request->shouldHaveIdentity = true;
		$request->identity = $identity;

		return $request;
	}

}
