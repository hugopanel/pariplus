<?php

function get_add() {
    $path    = '/assets/img/ads';
    $files = scandir($path);
    $files = array_diff(scandir($path), array('.', '..'));

    return $files[array_rand($files)];
}
