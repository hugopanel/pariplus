<?php

function get_ad($rootPath = '') {
    $path = $rootPath . 'assets/img/ads';
    $files = scandir($path);
    $files = array_diff(scandir($path), array('.', '..'));

    return $rootPath . "assets/img/ads/" . $files[array_rand($files)];
}
