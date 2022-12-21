<?php

namespace AgeGate\Integration;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Discover
{
    public function __construct()
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__FILE__)));

        $files = [];

        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }

            $class = trim(str_replace($file->getExtension(), '', $file->getFilename()), '.');


            $ns = 'AgeGate' . str_replace(AGE_GATE_PATH . 'src', '', $file->getPath()) . '/' . $class;

            $class = str_replace('/', '\\', $ns);

            if ($class === __CLASS__) {
                continue;
            }

            if (class_exists($class)) {
                (new $class)->init();
            }

            $files[] = $file->getPathname();
        }
    }
}
