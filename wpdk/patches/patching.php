<?php
/*
 * Copyright (c) 2026 by Dinh Thoai Tran <zinospetrel@sdf.org>.
 * All rights reserved.
 *
 * Source code of 'Wordpress On Disk' (wpdk) theme is licensed under the GPLv2.
 *
 * Viewing copyright information at https://github.com/condlex/wpdk/LICENSE .
 */

function wpdk_theme_patch_file( $patches_dir, $filename ) {
  $finding = file_get_contents( $patches_dir . '/' . $filename . '.key' );
  $replacement = file_get_contents( $patches_dir . '/' . $filename . '.repl' );
  $source_file = __DIR__ . '/../../../../wp-includes/' . $filename;
  $text = file_get_contents( $source_file );
  $position = strpos( $text, $replacement );
  if ( false !== $position ) return;
  $position = strpos( $text, $finding );
  if ( false === $position ) {
    return;
  }
  $text = substr( $text, 0, $position ) . $replacement . substr( $text, $position + strlen( $finding ) );
  file_put_contents( $source_file, $text );
}

function wpdk_theme_patching() {
  $patches_dir = __DIR__ . '/wp-includes';
  wpdk_theme_patch_file( $patches_dir, 'class-wp.php' );
  wpdk_theme_patch_file( $patches_dir, 'general-template.php' );
}

wpdk_theme_patching();

?>