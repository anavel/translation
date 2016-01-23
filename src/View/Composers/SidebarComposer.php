<?php
namespace Anavel\Translation\View\Composers;

use Request;
use URL;

class SidebarComposer
{
    public function compose($view)
    {
        $files = config('anavel-translation.files');

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
                    'route' => route('anavel-translation.file.edit', [$userFile]),
                    'name' => $userFile,
                    'isActive' => URL::current() === route('anavel-translation.file.edit', [$userFile])
                ];
            }
        }
        if (array_key_exists('vendor', $files)) {
            foreach ($files['vendor'] as $vendorKey => $vendorFile) {
                $items[] = [
                    'route' => route('anavel-translation.file.edit', [$vendorKey, $vendorFile]),
                    'name' => "$vendorKey : $vendorFile",
                    'isActive' => URL::current() === route('anavel-translation.file.edit', [$vendorKey, $vendorFile])
                ];
            }
        }

        $view->with([
            'items' => $items
        ]);
    }
}
