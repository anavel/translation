<?php


namespace Transleite\Tests;


use ANavallaSuiza\Transleite\Services\ConfigurationReader;

class ConfigurationReaderTest extends TestBase
{
    /** @var  ConfigurationReader */
    protected $sut;

    protected $config;

    public function setUp()
    {
        parent::setUp();

        $this->sut = new ConfigTraitClass();

        $this->config = require __DIR__ . '/config.php';
    }

    public function test_returns_null_if_config_not_set()
    {
        $value = $this->sut->getConfigValue('something');

        $this->assertNull($value);
    }

    public function test_returns_null_if_value_not_set()
    {
        $this->sut->config = [];

        $value = $this->sut->getConfigValue('something');

        $this->assertNull($value);
    }

    public function test_call_with_one_param()
    {
        $this->sut->config = $this->config;

        $value = $this->sut->getConfigValue('single');

        $this->assertEquals('one', $value);
    }

    public function test_call_with_two_params()
    {
        $this->sut->config = $this->config;

        $value = $this->sut->getConfigValue('nested', 'second-level');

        $this->assertEquals('two', $value);
    }

}

class ConfigTraitClass
{
    public $config;

    use ConfigurationReader;
}