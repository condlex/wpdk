<?php
/*
 * Copyright (c) 2026 by Dinh Thoai Tran <zinospetrel@sdf.org>.
 * All rights reserved.
 *
 * Source code of 'Wordpress On Disk' (wpdk) theme is licensed under the GPLv2.
 *
 * Viewing copyright information at https://github.com/condlex/wpdk/LICENSE .
 */

set_time_limit( 60 * 60 * 24 );
global $wpdk_config;
require_once __DIR__ . '/wpdk/config.php';

//$script_file = $wpdk_config['root_uri'] . '/wp-content/themes/wpdk/wpdk-t/';
$script_file = 'http://localhost/wpdk-t/';
$max_count = 5000;
$count = 1006;
do {
  $url = $script_file . '?bs=10000&bm=' . $max_count . '&bn=' . $count . '&ph=5';
  $result = file_get_contents($url);
  echo '[R] ', $count, ' / ', $max_count, '\n', $result;
  $count++;
} while ( $count <= $max_count );

?>
