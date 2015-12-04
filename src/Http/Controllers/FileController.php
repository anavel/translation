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
        $editLangsMissingKeys = [];
        $fallback = config('app.fallback_locale');

        foreach ($this->lang as $lang) {
            $file = empty($param2) ? $param : "$param::$param2";
            $transResult = trans($file, [], null, $lang);
            /*
             trans returns the original string if can't find the translation. Since we are not
            looking for a specifig line but a whole file, an array should be returned. If that's not the case, the file doesn't exist.
             */
            $editLangs[$lang] = is_array($transResult) ? $transResult : [];
        }

        // Add back keys from fallback_locale to other langs that don't have them. This way all langs have the same keys
        // and it is easier for the user to translate and keep track of them.
        foreach ($editLangs as $langKey => $lang) {
            if (! empty($editLangs[$fallback])) {
                if ($langKey === $fallback) {
                    continue;
                }
                $missingKeys = $this->arrayDiffKeyRecursive($editLangs[$fallback], $editLangs[$langKey]);

                if (! empty($missingKeys)) {
                    foreach (array_keys($missingKeys) as $missingKey) {
                        $editLangs[$langKey][$missingKey] = $editLangs[$fallback][$missingKey];
                    }
                    $editLangsMissingKeys[$langKey] = array_keys($missingKeys);
                }
            }
            array_multisort($editLangs[$langKey]);
        }


        return View::make('transleite::pages.edit', compact('editLangs', 'editLangsMissingKeys'));
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
            $translation = $this->arrayFilterRecursive($translations[$lang]);
            $fileRoute = empty($param2) ? $lang . '/' . $param . '.php' : 'vendor/' . $param . '/' . $lang . '/' . $param2 . '.php';
            $string = "<?php" . PHP_EOL . PHP_EOL;
            $string .= 'return ';
            $string .= var_export($translation, true) . ';';
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

    private function arrayDiffKeyRecursive(array $arr1, array $arr2) {
        $diff = array_diff_key($arr1, $arr2);
        $intersect = array_intersect_key($arr1, $arr2);

        foreach ($intersect as $k => $v) {
            if (is_array($arr1[$k]) && is_array($arr2[$k])) {
                $d = $this->arrayDiffKeyRecursive($arr1[$k], $arr2[$k]);

                if ($d) {
                    $diff[$k] = $d;
                }
            }
        }

        return $diff;
    }

    private function arrayFilterRecursive(array $array)
    {
        foreach ($array as &$value)
        {
            if (is_array($value))
            {
                $value = $this->arrayFilterRecursive($value);
            }
        }

        return array_filter($array);
    }
}