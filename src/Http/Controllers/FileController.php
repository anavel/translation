<?php


namespace ANavallaSuiza\Transleite\Http\Controllers;

use ANavallaSuiza\Adoadomin\Http\Controllers\Controller;
use RedirectResponse;
use Storage;
use View;

class FileController extends Controller
{
    protected $lang = [];
    protected $config = [];

    public function __construct()
    {
        $this->lang = config('adoadomin.translation_languages');
        $this->config = config('transleite.files');
    }

    public function edit($fileKey)
    {
        /*
         *
         * config deberia ter un indice de arquivos de usuario (en resources/lang) e arquivos de vendor (en resources/lang/vendor)
         * debería haber un arquivo por cada idioma (na súa carpeta). Se o arquivo non existe, créao. se non se establece tradución, a clave NON SE METE
         *
         *
         *
         */

        $editLangs = [];

        foreach ($this->lang as $lang) {
            $transResult = trans($fileKey, [], null, $lang);
            /*
             trans returns the original string if can't find the translation. Since we are not
            looking for a specifig line but a whole file, an array should be returned. If that's not the case, the file doesn't exist.
             */
            $editLangs[$lang] = is_array($transResult) ? $transResult : [];
        }

        return View::make('transleite::pages.edit', compact('editLangs'));
    }
}