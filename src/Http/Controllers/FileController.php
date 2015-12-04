<?php


namespace ANavallaSuiza\Transleite\Http\Controllers;

use ANavallaSuiza\Adoadomin\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function edit($param, $param2 = null)
    {
        $editLangs = [];

        foreach ($this->lang as $lang) {
            $file = empty($param2) ? $param : "$param::$param2";
            $transResult = trans($file, [], null, $lang);
            /*
             trans returns the original string if can't find the translation. Since we are not
            looking for a specifig line but a whole file, an array should be returned. If that's not the case, the file doesn't exist.
             */
            $editLangs[$lang] = is_array($transResult) ? $transResult : [];
        }

        return View::make('transleite::pages.edit', compact('editLangs'));
    }

    public function update(Request $request, $param, $param2 = null)
    {
        if (! $request->has('translations')) {
            session()->flash('adoadomin-alert', [
                'type'  => 'error',
                'icon'  => 'fa-error',
                'title' => trans('transleite::messages.alert_empty_translations_title'),
                'text'  => trans('transleite::messages.alert_empty_translations_text')
            ]);

            return redirect()->back()->withInput();
        }

        $translations = $request->input('translations');

        $diskDriver = config('transleite.filedriver');
        if (empty($diskDriver)) {
            throw new \Exception('filedriver should be set in config');
        }
        $disk = Storage::disk($diskDriver);

        foreach ($this->lang as $lang) {
            $fileRoute = empty($param2) ? $lang . '/' . $param . '.php' : 'vendor/' . $param .'/' . $lang . '/' . $param2 . '.php';
            $string = "<?php" . PHP_EOL . PHP_EOL;
            $string .= 'return ';
            $string .= var_export($translations[$lang], true) . ';';
            $disk->put($fileRoute, $string);
        }


        session()->flash('adoadomin-alert', [
            'type'  => 'success',
            'icon'  => 'fa-check',
            'title' => trans('transleite::messages.alert_translations_saved_title'),
            'text'  => trans('transleite::messages.alert_translations_saved_text')
        ]);
        return redirect()->back();
    }
}