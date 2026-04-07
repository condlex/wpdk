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

$token = $_GET['token'];
if ( PHP_SAPI === 'cli' ) {
  $token = $argv[1];
}
if ( $token !== $wpdk_config['token'] ) {
  header( 'Content-Type: text/plain' );
  echo "Token is not valid!";
  exit();
}

$debug_count = 1;
//$script_file = $wpdk_config['root_uri'] . '/wp-content/themes/wpdk/wpdk-t/prepare.php';
$script_file = 'http://localhost/wpdk-t/prepare.php';
$batch_size = 10000;
$batch_size = 100 / 4;
$max_count = 500000 * 100 * 4;
$count = 489;
do {
  $url = $script_file . '?bs=' . $batch_size . '&bn=' . $count;
  $result = @file_get_contents($url);
  $mark_count = -1;
  do {
    $mark_count = -1;
    $lines = explode( '\n', $result );
    foreach ( $lines as $line_item ) {
      $fields = explode( ' ', $line_item );
      if ( count( $fields ) < 3 ) continue;
      if ( $fields[0] !== '[L]' || $fields[2] !== '[L]' ) continue;
      $mark_count = intval( $fields[1] );
      break;
    }
    if ( $mark_count >= 0 ) {
      $url = $script_file . '?bs=' . $batch_size . '&bn=' . $count . '&mk=' . $mark_count;
      $result = @file_get_contents($url);
      echo '[RI] ', $result, '\n';
    }
  } while ( $mark_count >= 0 );
  if ( $count % $debug_count === 0 ) {
    echo '[R] ', $count, ' / ', $max_count, '\n', $result;
  }
  $count++;
} while ( $count <= $max_count );

?>
