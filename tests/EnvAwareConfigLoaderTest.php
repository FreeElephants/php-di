<?php

namespace FreeElephants\DI;

class EnvAwareConfigLoaderTest extends AbstractTestCase
{

    public function testLoadComponentsWithEnv()
    {
        $configLoader = new EnvAwareConfigLoader(self::FIXTURE_PATH);
        putenv('ENV=test');
        $components = $configLoader->readConfig('components');
        $this->assertArrayHasKey('foo', $components);
        $this->assertArrayHasKey('baz', $components);
        $this->assertSame($components['baz'], 'test-baz');
    }

    public function testLoadComponentsWithMissingEnvExtention()
    {
        $configLoader = new EnvAwareConfigLoader(self::FIXTURE_PATH);
        putenv('ENV=missing');
        $components = $configLoader->readConfig('components');
        $this->assertArrayHasKey('foo', $components);
        $this->assertArrayHasKey('baz', $components);
        $this->assertSame($components['baz'], 'baz');
    }

    public function testExceptionOnMissingCommonFile()
    {
        $configLoader = new EnvAwareConfigLoader(self::FIXTURE_PATH);
        $this->expectException(\RuntimeException::class);
        $configLoader->readConfig('missing-scope');
    }

    public function testReadWithMergeNested()
    {
        $configLoader = new EnvAwareConfigLoader(self::FIXTURE_PATH);
        putenv('ENV=test');
        $components = $configLoader->readConfig('components-nested');
        $this->assertSame('bar-test', $components['level-1']['level-2']['foo']);
    }

}
