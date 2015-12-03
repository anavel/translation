<?php
namespace Transleite\Tests;

use Orchestra\Testbench\TestCase;
use Mockery;

abstract class TestBase extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        include __DIR__.'/../src/Http/routes.php';
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('adoadomin.translation_languages', ['gl', 'en', 'es']);
    }

    protected function getPackageProviders($app)
    {
        return ['ANavallaSuiza\Transleite\TransleiteModuleProvider'];
    }


    public function mock($className)
    {
        return Mockery::mock($className);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
