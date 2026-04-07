<?php
/*
 * Copyright (c) 2026 by Dinh Thoai Tran <zinospetrel@sdf.org>.
 * All rights reserved.
 *
 * Source code of 'Wordpress On Disk' (wpdk) theme is licensed under the GPLv2.
 *
 * Viewing copyright information at https://github.com/condlex/wpdk/LICENSE .
 */

set_time_limit( 60 * 60 * 18 );
ini_set('display_errors', 1);
error_reporting(E_ALL);
global $wpdk_config;
require_once __DIR__ . '/wpdk/config.php';
require_once __DIR__ . '/wpdk/wpdk.php';

$token = '';
if ( isset( $_GET['token'] ) ) {
  $token = $_GET['token'];
}
if ( $token !== $wpdk_config['token'] ) {
  header( 'Content-Type: text/plain' );
  echo "Token is not valid!";
  exit();
}

if ( function_exists('opcache_reset') ) {
  opcache_reset();
}

$debug_count = 1000;
$maximum_execution_time = 60 * 60 * 18;

$batch_size = intval( $_GET['bs'] );
if ( $batch_size <= 0 ) {
  $batch_size = 1000;
}
$batch_no = intval( $_GET['bn'] );
if ( $batch_no <= 0 ) {
  $batch_no = 1;
}
$from_count = ( $batch_no - 1 ) * $batch_size + 1;
$to_count = ( $batch_no ) * $batch_size;

$mark_count = 0;
if ( isset( $_GET['mk'] ) ) {
  $mark_count = intval( $_GET['mk'] );
}
if ( $mark_count <= 0 ) {
  $mark_count = $from_count;
}

$start_time = microtime(true);
$id = $from_count;
while ( $id <= $to_count ) {
  $end_time = microtime(true);
  $time = ( $end_time - $start_time ) / ( 1000 * 1000 );
  if ( $time > $maximum_execution_time ) {
    echo '[T] ', $time, '\n';
    break;
  }
  if ( $id < $mark_count ) {
    $id = $id + 1;
    continue;
  }
  wpdk_search_index_post( $id );
  if ( $id % $debug_count === 0 ) {
    echo '[C] ', $id, " is indexed! [ $id / $from_count, $to_count ].", "\n";
  }
  $id = $id + 1;
}
if ( $id <= $to_count ) {
  echo '\n[L] ', $id, ' [L]\n';
} else {
  echo '[IDX] ', $id, ', ', $from_count, ' - ', $to_count, ' / ', $batch_no, ' / ', $batch_size, '\n';
}
?>
