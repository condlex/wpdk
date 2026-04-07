<?php
/*
 * Copyright (c) 2026 by Dinh Thoai Tran <zinospetrel@sdf.org>.
 * All rights reserved.
 *
 * Source code of 'Wordpress On Disk' (wpdk) theme is licensed under the GPLv2.
 *
 * Viewing copyright information at https://github.com/condlex/wpdk/LICENSE .
 */

global $wpdk_config;
require_once __DIR__ . '/../wpdk/config.php';

$token = $_GET['token'];
if ( PHP_SAPI === 'cli' ) {
  $token = $argv[1];
}
if ( $token !== $wpdk_config['token'] ) {
  header( 'Content-Type: text/plain' );
  echo "Token is not valid!";
  exit();
}

function wpdk_theme_unpatch_file( $patches_dir, $filename, $source_dir = '/../../../../wp-includes/' ) {
  $finding = file_get_contents( $patches_dir . '/' . $filename . '.repl' );
  $replacement = file_get_contents( $patches_dir . '/' . $filename . '.key' );
  $source_file = __DIR__ . $source_dir . $filename;
  $text = file_get_contents( $source_file );
  $position = strpos( $text, $finding );
  if ( false === $position ) {
    return;
  }
  $text = substr( $text, 0, $position ) . $replacement . substr( $text, $position + strlen( $finding ) );
  file_put_contents( $source_file, $text );
}

function wpdk_theme_unpatching() {
  $patches_dir = __DIR__ . '/wp-includes';
  wpdk_theme_unpatch_file( $patches_dir, 'class-wp.php' );
  wpdk_theme_unpatch_file( $patches_dir, 'general-template.php' );

  $patches_dir = __DIR__ . '/wp-admin';
  wpdk_theme_unpatch_file( $patches_dir, 'admin.php', '/../../../../wp-admin/' );
  wpdk_theme_unpatch_file( $patches_dir, 'edit.php', '/../../../../wp-admin/' );
  wpdk_theme_unpatch_file( $patches_dir, 'post-new.php', '/../../../../wp-admin/' );
}

wpdk_theme_unpatching();

?>