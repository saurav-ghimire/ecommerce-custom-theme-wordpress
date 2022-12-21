<?php

namespace Asylum\Utility;

class Storage
{
    public static function uploadDir($type = 'basedir')
    {
        $uploads = wp_upload_dir();
        return $uploads[$type];
    }

    public static function storageDir($folder = false, $baseFolder = 'storage')
    {
        $base = self::uploadDir();
        $baseFolder = trailingslashit($baseFolder);
        $storage = trailingslashit($base) . $baseFolder;

        if ($folder) {
            $storage .= $folder;
        }

        if (!is_dir($storage)) {
            wp_mkdir_p($storage);
        }

        return $storage;
    }

    public static function storageUrl($folder = false, $baseFolder = 'storage')
    {
        $base = self::uploadDir('baseurl');
        $baseFolder = trailingslashit($baseFolder);
        $storage = trailingslashit($base) . $baseFolder;

        if ($folder) {
            $storage .= $folder;
        }

        if (!is_dir($storage)) {
            wp_mkdir_p($storage);
        }

        return $storage;
    }
}
