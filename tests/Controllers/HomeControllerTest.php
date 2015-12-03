<?php


namespace Transleite\Tests;



use ANavallaSuiza\Transleite\Http\Controllers\HomeController;

class HomeControllerTest extends TestBase
{
    /** @var  HomeController */
    protected $sut;
    /** @var  array */
    protected $config;



    public function setUp()
    {
        parent::setUp();
        $this->config = [
            'user' => [
                'one'
            ],

            'vendor' => [
                'vendorname' => 'vendorfile'
            ]
        ];

        $this->sut = new HomeController();
    }

    public function test_is_instance_of_controller()
    {
        $this->assertInstanceOf('ANavallaSuiza\Adoadomin\Http\Controllers\Controller', $this->sut);
    }

    public function test_edit_throws_exception_if_no_files_configured()
    {
        $this->setExpectedException('Exception', 'No files configured');

        $this->sut->index('test');
    }

    public function test_edit_throws_exception_if_files_is_not_array()
    {
        $this->setExpectedException('Exception', 'Files should be an array');
        config(['transleite.files' => 'Chompy']);

        $this->sut->index('test');
    }

    public function test_edit_throws_exception_if_user_nor_vendor_are_found()
    {
        $this->setExpectedException('Exception', '"user" or "vendor" files should be set');
        config(['transleite.files' => ['test']]);

        $this->sut->index('test');
    }

    public function test_redirects_if_files_configured()
    {
        config(['transleite.files' => $this->config]);

        $response = $this->sut->index();

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
    }
}