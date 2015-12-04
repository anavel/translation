<?php
namespace ANavallaSuiza\Transleite\View\Composers;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory as ModelAbstractorFactory;
use Request;
use URL;

class SidebarComposer
{
    public function compose($view)
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

        $items = [];
        if (array_key_exists('user', $files)) {
            foreach ($files['user'] as $userFile) {
                $items[] = [
                    'route' => route('transleite.file.edit', [$userFile]),
                    'name' => $userFile,
                    'isActive' => URL::current() === route('transleite.file.edit', [$userFile])
                ];
            }
        }
        if (array_key_exists('vendor', $files)) {
            foreach ($files['vendor'] as $vendorKey => $vendorFile) {
                $items[] = [
                    'route' => route('transleite.file.edit', [$vendorKey, $vendorFile]),
                    'name' => "$vendorKey : $vendorFile",
                    'isActive' => URL::current() === route('transleite.file.edit', [$vendorKey, $vendorFile])
                ];
            }
        }

        $view->with([
            'items' => $items
        ]);
    }
}
