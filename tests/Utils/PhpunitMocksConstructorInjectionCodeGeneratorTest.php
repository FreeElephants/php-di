<?php

namespace FreeElephants\DI\Utils;

use FreeElephants\DI\AbstractTestCase;

class PhpunitMocksConstructorInjectionCodeGeneratorTest extends AbstractTestCase
{

	public function testGenerateCreateInstanceMethodWithMockedConstructorNullableArgs()
	{
		$generator = new PhpunitMocksConstructorInjectionCodeGenerator();
		
		$result = $generator->generateCreateInstanceMethodWithMockedConstructorNullableArgs(\Fixture\SomeService::class);

		$this->assertSame(
<<<'PHP'
private function createSomeService(
	?\Fixture\AnotherServiceInterface $anotherService = null,
	): \Fixture\SomeService {
	return new \Fixture\SomeService(
		$anotherService ?: $this->createMock(\Fixture\AnotherServiceInterface::class),
	);
}

PHP,
		$result
);
	}
}
