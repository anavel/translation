<?php
namespace Anavel\Translation\Http\Controllers;

use Anavel\Foundation\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RedirectResponse;
use Storage;
use View;

class FileController extends Controller
{
    protected $lang = [];
    protected $config = [];
    protected $fallback;

    public function __construct()
    {
        $this->lang = config('anavel.translation_languages');
        $this->config = config('anavel-translation.files');
        $this->fallback = config('app.fallback_locale');
    }

    public function edit($param, $param2 = null)
    {
        $editLangsMissingKeys = [];

        $editLangs = $this->getLangsFromFile($param, $param2);

        // Add back keys from fallback_locale to other langs that don't have them. This way all langs have the same keys
        // and it is easier for the user to translate and keep track of them.
        foreach ($editLangs as $langKey => $lang) {
            if (! empty($editLangs[$this->fallback])) {
                if ($langKey === $this->fallback) {
                    $this->ksortTree($editLangs[$langKey]);
                    continue;
                }
                $missingKeys = $this->arrayDiffKeyRecursive($editLangs[$this->fallback], $editLangs[$langKey]);

                if (! empty($missingKeys)) {
                    foreach (array_keys($missingKeys) as $missingKey) {
                        $editLangs[$langKey][$missingKey] = $editLangs[$this->fallback][$missingKey];
                    }
                    $editLangsMissingKeys[$langKey] = array_keys($missingKeys);
                }
            }
            $this->ksortTree($editLangs[$langKey]);
        }


        return View::make('anavel-translation::pages.edit', compact('editLangs', 'editLangsMissingKeys'));
    }

    public function update(Request $request, $param, $param2 = null)
    {
        if (! $request->has('translations')) {
            session()->flash('anavel-alert', [
                'type'  => 'error',
                'icon'  => 'fa-error',
                'title' => trans('anavel-translation::messages.alert_empty_translations_title'),
                'text'  => trans('anavel-translation::messages.alert_empty_translations_text')
            ]);

            return redirect()->back()->withInput();
        }

        $translations = $request->input('translations');

        $disk = $this->getDisk();

        foreach ($this->lang as $lang) {
            if (! empty($translations[$lang])) {
                $translation = $this->arrayFilterRecursive($translations[$lang]);
                $fileRoute = empty($param2) ? $lang . '/' . $param . '.php' : 'vendor/' . $param . '/' . $lang . '/' . $param2 . '.php';
                $string = "<?php" . PHP_EOL . PHP_EOL;
                $string .= 'return ';
                $string .= var_export($translation, true) . ';';
                $disk->put($fileRoute, $string);
            }
        }


        session()->flash('anavel-alert', [
            'type'  => 'success',
            'icon'  => 'fa-check',
            'title' => trans('anavel-translation::messages.alert_translations_saved_title'),
            'text'  => trans('anavel-translation::messages.alert_translations_saved_text')
        ]);

        return redirect(null, 200)->route('anavel-translation.file.edit', [$param, $param2]);
    }

    public function create(Request $request, $param, $param2 = null)
    {
        if (! $request->has('translations-new')) {
            session()->flash('anavel-alert', [
                'type'  => 'error',
                'icon'  => 'fa-error',
                'title' => trans('anavel-translation::messages.alert_empty_new_translations_title'),
                'text'  => trans('anavel-translation::messages.alert_empty_new_translations_text')
            ]);

            return redirect()->back()->withInput();
        }

        $newLine = $request->input('translations-new');


        $langs = $this->getLangsFromFile($param, $param2);

        $disk = $this->getDisk();

        //We'll only add this line to the main lang
        $translation = $langs[$this->fallback];

        //http://stackoverflow.com/questions/1417019/how-to-recursively-create-a-multidimensional-array#1417033

        $key = $newLine['key'];
        $val = $newLine['value'];
        $path = explode('.', $key);

        $newTranslation = array();
        $tmp = &$newTranslation;
        foreach ($path as $segment) {
            $tmp[$segment] = array();
            $tmp = &$tmp[$segment];
        }
        $tmp = $val;

        $translation[key($newTranslation)] = $newTranslation[key($newTranslation)];


        $fileRoute = empty($param2) ? $this->fallback . '/' . $param . '.php' : 'vendor/' . $param . '/' . $this->fallback . '/' . $param2 . '.php';
        $string = "<?php" . PHP_EOL . PHP_EOL;
        $string .= 'return ';
        $string .= var_export($translation, true) . ';';
        $disk->put($fileRoute, $string);

        session()->flash('anavel-alert', [
            'type'  => 'success',
            'icon'  => 'fa-check',
            'title' => trans('anavel-translation::messages.alert_translations_saved_title'),
            'text'  => trans('anavel-translation::messages.alert_translations_saved_text')
        ]);

        return redirect(null, 200)->route('anavel-translation.file.edit', [$param, $param2]);
    }

    protected function getDisk()
    {
        $diskDriver = config('anavel-translation.filedriver');
        if (empty($diskDriver)) {
            throw new \Exception('filedriver should be set in config');
        }

        return Storage::disk($diskDriver);
    }

    protected function getLangsFromFile($param, $param2 = null)
    {
        $langs = [];
        foreach ($this->lang as $lang) {
            $file = empty($param2) ? $param : "$param::$param2";
            $transResult = trans($file, [], null, $lang);
            /*
             trans returns the original string if it can't find the translation. Since we are not
            looking for a specific line but a whole file, an array should be returned. If that's not the case, the file doesn't exist.
             */
            $langs[$lang] = is_array($transResult) ? $transResult : [];
        }

        return $langs;
    }

    protected function arrayDiffKeyRecursive(array $arr1, array $arr2)
    {
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

    protected function arrayFilterRecursive(array $array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = $this->arrayFilterRecursive($value);
            }
        }

        return array_filter($array);
    }

    /**
     * @return bool
     * @author  Kevin van Zonneveld &lt;kevin@vanzonneveld.net>
     * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
     * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
     * @version   SVN: Release: $Id: ksortTree.inc.php 223 2009-01-25 13:35:12Z kevin $
     * @link      http://kevin.vanzonneveld.net/
     *
     * @param array $array
     */
    protected function ksortTree(&$array)
    {
        if (! is_array($array)) {
            return false;
        }

        ksort($array);
        foreach ($array as $k => $v) {
            $this->ksortTree($array[$k]);
        }

        return true;
    }
}
