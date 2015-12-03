<?php


namespace ANavallaSuiza\Transleite\Http\Controllers;

use ANavallaSuiza\Adoadomin\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function index()
    {
        $files = config('transleite.files');

        if (empty($files)) {
            throw new \Exception("No files configured.");
        }

        if (! is_array($files)) {
            throw new \Exception('Files should be an array');
        }

        $files = array_change_key_case($files, CASE_LOWER);
        if (! array_key_exists('user', $files) && ! array_key_exists('vendor', $files)) {
            throw new \Exception('"user" or "vendor" files should be set');
        }

        return new RedirectResponse(route('transleite.file.edit', key($files)));
    }
}