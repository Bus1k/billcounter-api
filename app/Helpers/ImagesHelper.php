<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImagesHelper
{
    public static function getGoogleImage(string $fileName)
    {
        $dir = '/';
        $recursive = false; // Get subdirectories also?
        $contents = collect(Storage::cloud()->listContents($dir, $recursive));

        $file = $contents
            ->where('type', '=', 'file')
            ->where('filename', '=', pathinfo($fileName, PATHINFO_FILENAME))
            ->where('extension', '=', pathinfo($fileName, PATHINFO_EXTENSION))
            ->first();

        return $file;
    }

    public static function changeImagePermission($photoData)
    {
        $service = Storage::cloud()->getAdapter()->getService();
        $permission = new \Google_Service_Drive_Permission();
        $permission->setRole('reader');
        $permission->setType('anyone');
        $permission->setAllowFileDiscovery(false);
        $service->permissions->create($photoData['basename'], $permission);

        return true;
    }

}
