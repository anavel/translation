<?php


namespace Transleite\Tests;

use ANavallaSuiza\Transleite\Http\Controllers\FileController;
use Storage;

class FileControllerTest extends TestBase
{
    /** @var  FileController */
    protected $sut;
    /** @var  array */
    protected $config;


    public function setUp()
    {
        parent::setUp();

        config(['adoadomin.translation_languages' => ['en', 'es']]);
        config(['transleite.filedriver' => 'diskdriver']);


        $this->sut = new FileController();

        $this->config = [
            'user' => [
                'one'
            ],

            'vendor' => [
                'vendorname' => 'vendorfile'
            ]
        ];
    }

    public function test_edit_returns_array_when_file_empty()
    {
        config(['transleite.files' => $this->config]);

        $result = $this->sut->edit('test');

        $this->assertObjectHasAttribute('data', $result);
        $this->assertTrue($result->offsetExists('editLangs'));
        $viewData = $result->offsetGet('editLangs');
        $this->assertArrayHasKey('en', $viewData);
        $this->assertArrayHasKey('es', $viewData);

        $this->assertInternalType('array', $viewData['en']);
        $this->assertInternalType('array', $viewData['es']);
    }

    public function test_edit_returns_array_with_files_contents()
    {
        \App::instance('translator', $transMock = $this->mock('Illuminate\Filesystem\Filesystem\FileLoader'));
        config(['transleite.files' => $this->config]);

        $transMock->shouldReceive('trans')->andReturn(['yeah' => 'yeah']);

        $result = $this->sut->edit('test');

        $this->assertObjectHasAttribute('data', $result);
        $this->assertTrue($result->offsetExists('editLangs'));
        $viewData = $result->offsetGet('editLangs');
        $this->assertArrayHasKey('en', $viewData);
        $this->assertArrayHasKey('es', $viewData);

        $this->assertInternalType('array', $viewData['en']);
        $this->assertInternalType('array', $viewData['es']);

        $this->assertArrayHasKey('yeah', $viewData['en']);
    }

    public function test_calls_vendor_file_when_param2_not_empty()
    {
        \App::instance('translator', $transMock = $this->mock('Illuminate\Filesystem\Filesystem\FileLoader'));
        config(['transleite.files' => $this->config]);

        $transMock->shouldReceive('trans')->with('vendor::test', [], null,
            \Mockery::any())->andReturn(['yeah' => 'yeah']);

        $result = $this->sut->edit('vendor', 'test');

        $this->assertObjectHasAttribute('data', $result);
        $this->assertTrue($result->offsetExists('editLangs'));
        $viewData = $result->offsetGet('editLangs');
        $this->assertArrayHasKey('en', $viewData);
        $this->assertArrayHasKey('es', $viewData);

        $this->assertInternalType('array', $viewData['en']);
        $this->assertInternalType('array', $viewData['es']);

        $this->assertArrayHasKey('yeah', $viewData['en']);
    }

    public function test_update_bails_if_empty_array()
    {
        $requestMock = $this->mock('Illuminate\Http\Request');

        $requestMock->shouldReceive('has')->with('translations')->times(1)->andReturn(false);

        $result = $this->sut->update($requestMock, 'test');

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $result);
        $this->assertTrue($result->getSession()->has('adoadomin-alert'));

        $alert = $result->getSession()->get('adoadomin-alert');

        $this->assertEquals('error', $alert['type']);
    }

    public function test_update_saves_array_to_file()
    {
        $requestMock = $this->mock('Illuminate\Http\Request');

        $requestMock->shouldReceive('has')->with('translations')->times(1)->andReturn(true);
        $requestMock->shouldReceive('input')->with('translations')->times(1)->andReturn($returnArray = [
            'en' => [
                'key' => 'value'
            ],
            'es' => [
                'key' => 'value'
            ]
        ]);

        Storage::shouldReceive('disk')->times(1)->with('diskdriver')->andReturn(\Mockery::self());
        Storage::shouldReceive('put')->times(1)->with('en/test.php', \Mockery::any())->andReturn(\Mockery::self());
        Storage::shouldReceive('put')->times(1)->with('es/test.php', \Mockery::any())->andReturn(\Mockery::self());

        $result = $this->sut->update($requestMock, 'test');

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $result);
        $this->assertTrue($result->getSession()->has('adoadomin-alert'));

        $alert = $result->getSession()->get('adoadomin-alert');

        $this->assertEquals('success', $alert['type']);
    }

    public function test_update_saves_array_to_vendor_file()
    {
        $requestMock = $this->mock('Illuminate\Http\Request');

        $requestMock->shouldReceive('has')->with('translations')->times(1)->andReturn(true);
        $requestMock->shouldReceive('input')->with('translations')->times(1)->andReturn($returnArray = [
            'en' => [
                'key' => 'value'
            ],
            'es' => [
                'key' => 'value'
            ]
        ]);

        Storage::shouldReceive('disk')->times(1)->with('diskdriver')->andReturn(\Mockery::self());
        Storage::shouldReceive('put')->times(1)->with('vendor/vendorname/en/test.php', \Mockery::any())->andReturn(\Mockery::self());
        Storage::shouldReceive('put')->times(1)->with('vendor/vendorname/es/test.php', \Mockery::any())->andReturn(\Mockery::self());

        $result = $this->sut->update($requestMock, 'vendorname', 'test');

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $result);
        $this->assertTrue($result->getSession()->has('adoadomin-alert'));

        $alert = $result->getSession()->get('adoadomin-alert');

        $this->assertEquals('success', $alert['type']);
    }

    public function test_update_filters_empty_keys()
    {
        $requestMock = $this->mock('Illuminate\Http\Request');

        $requestMock->shouldReceive('has')->with('translations')->times(1)->andReturn(true);
        $requestMock->shouldReceive('input')->with('translations')->times(1)->andReturn($returnArray = [
            'en' => [
                'key' => 'value',
                'otherkey' => ''
            ],
            'es' => [
                'key' => 'value'
            ]
        ]);

        Storage::shouldReceive('disk')->times(1)->with('diskdriver')->andReturn(\Mockery::self());
        Storage::shouldReceive('put')->times(1)->with('vendor/vendorname/en/test.php', '<?php

return array (
  \'key\' => \'value\',
);')->andReturn(\Mockery::self());
        Storage::shouldReceive('put')->times(1)->with('vendor/vendorname/es/test.php', \Mockery::any())->andReturn(\Mockery::self());

        $result = $this->sut->update($requestMock, 'vendorname', 'test');

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $result);
        $this->assertTrue($result->getSession()->has('adoadomin-alert'));

        $alert = $result->getSession()->get('adoadomin-alert');

        $this->assertEquals('success', $alert['type']);
    }

    public function test_update_merges_keys_from_fallback_locale()
    {
        
    }

    public function test_throws_exception_if_disk_not_set()
    {
        $this->setExpectedException('Exception', 'filedriver should be set in config');

        $requestMock = $this->mock('Illuminate\Http\Request');

        config(['transleite.filedriver' => null]);


        $requestMock->shouldReceive('has')->with('translations')->times(1)->andReturn(true);
        $requestMock->shouldReceive('input')->with('translations')->times(1)->andReturn($returnArray = [
            'en' => [
                'key' => 'value'
            ],
            'es' => [
                'key' => 'value'
            ]
        ]);

        $result = $this->sut->update($requestMock, 'test');
    }
}