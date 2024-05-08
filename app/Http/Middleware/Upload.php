<?php

namespace App\Http\Middleware;

use App\Facades\UtilityFacades;
use Closure;
class Upload
{
    public function handle($request, Closure $next)
    {
        if (!file_exists(storage_path() . "/installed")) {
            header('location:install');
            die;
        }

        config([
            'filesystems.default'                                               => (UtilityFacades::fileSystemSettings('storage_type') != '') ? UtilityFacades::fileSystemSettings('storage_type') : 'local',
            'filesystems.disks.s3.key'                                          => UtilityFacades::fileSystemSettings('s3_key'),
            'filesystems.disks.s3.secret'                                       => UtilityFacades::fileSystemSettings('s3_secret'),
            'filesystems.disks.s3.region'                                       => UtilityFacades::fileSystemSettings('s3_region'),
            'filesystems.disks.s3.bucket'                                       => UtilityFacades::fileSystemSettings('s3_bucket'),
            'filesystems.disks.s3.url'                                          => UtilityFacades::fileSystemSettings('s3_url'),
            'filesystems.disks.s3.endpoint'                                     => UtilityFacades::fileSystemSettings('s3_endpoint'),


            'filesystems.disks.wasabi.key'                                      => UtilityFacades::fileSystemSettings('wasabi_key'),
            'filesystems.disks.wasabi.secret'                                   => UtilityFacades::fileSystemSettings('wasabi_secret'),
            'filesystems.disks.wasabi.region'                                   => UtilityFacades::fileSystemSettings('wasabi_region'),
            'filesystems.disks.wasabi.bucket'                                   => UtilityFacades::fileSystemSettings('wasabi_bucket'),
            'filesystems.disks.wasabi.endpoint'                                 => UtilityFacades::fileSystemSettings('wasabi_root'),
        ]);
        return $next($request);
    }
}
