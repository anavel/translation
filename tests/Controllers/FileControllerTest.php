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

    public function test_returns_array_when_file_empty()
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

    public function test_returns_array_with_files_contents()
    {
        \App::instance('translator', $transMock = $this->mock('Illuminate\Filesystem\Filesystem\FileLoader'));
        config(['transleite.files' => $this->config]);

        $transMock->shouldReceive('trans')->andReturn(['yeah']);

        $result = $this->sut->edit('test');

        $this->assertObjectHasAttribute('data', $result);
        $this->assertTrue($result->offsetExists('editLangs'));
        $viewData = $result->offsetGet('editLangs');
        $this->assertArrayHasKey('en', $viewData);
        $this->assertArrayHasKey('es', $viewData);

        $this->assertInternalType('array', $viewData['en']);
        $this->assertInternalType('array', $viewData['es']);
    }

//    public function test_reads_from_local_filedriver_if_not_set()
//    {
//        Storage::shouldReceive('disk')->with('local')->times(1);
//        config(['transleite.files' => $this->config]);
//
//        $this->sut->edit('one');
//    }
//
//    public function test_reads_from_config_filedriver_if_set()
//    {
//        Storage::shouldReceive('disk')->with('driver')->times(1);
//        config(['transleite.files' => array_merge($this->config, ['filedriver' => 'driver'])]);
//
//        $this->sut->edit('one');
//    }
}