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
if ( PHP_SAPI == 'cli' ) {
  $token = $argv[1];
}
if ( $token !== $wpdk_config['token'] ) {
  header( 'Content-Type: text/plain' );
  echo "Token is not valid!";
  exit();
}

if ( function_exists( 'opcache_reset' ) ) {
  opcache_reset();
  echo "OPcache cleared successfully.";
} else {
  echo "OPcache is not enabled.";
}
?>
