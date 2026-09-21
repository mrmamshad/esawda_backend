<?php

return [
    /*
     * The current downloadable product version. The release ZIP is expected at
     * storage/app/releases/{name}-v{version}.zip (built by `release:build`).
     */
    'name' => env('DOWNLOAD_PACKAGE_NAME', 'esawda-marketplace'),
    'version' => env('DOWNLOAD_PACKAGE_VERSION', '1.0.0'),

    /*
     * How long a signed download URL stays valid after the buyer validates
     * their license (minutes).
     */
    'link_ttl_minutes' => (int) env('DOWNLOAD_LINK_TTL', 30),

    /*
     * When true, the file is handed off to nginx via X-Accel-Redirect (the
     * scale-safe path for large files). The internal location below must map
     * to storage/app/releases. When false, PHP streams the file directly
     * (fine for local/dev).
     */
    'use_x_accel' => (bool) env('DOWNLOAD_X_ACCEL', false),
    'x_accel_location' => env('DOWNLOAD_X_ACCEL_LOCATION', '/protected-releases'),
];
