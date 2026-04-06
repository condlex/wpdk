<?php
/*
 * Copyright (c) 2026 by Dinh Thoai Tran <zinospetrel@sdf.org>.
 * All rights reserved.
 *
 * Source code of 'Wordpress On Disk' (wpdk) theme is licensed under the GPLv2.
 *
 * Viewing copyright information at https://github.com/condlex/wpdk/LICENSE .
 */

global $wpdk_config, $testor, $root_uri, $test_start, $test_count, $friendly;
require_once __DIR__ . '/wpdk/config.php';
require_once __DIR__ . '/wpdk/class-testor.php';

$friendly = false;
$test_count = 1000000;
$test_start = 1;
$test_count = 100;
$root_uri = $wpdk_config['root_uri'] . '/';
$verbose = false;
$no_test = false;
$no_time = false;
$testor = new \testor\Testor( $verbose, $no_test, $no_time );

function test_with_id( $id ) {
  global $testor, $root_uri, $friendly;

  if ( $friendly ) {
    $url = $root_uri . 'wpdk/' . $id . '/';
  } else {
    $url = $root_uri . '?wpdk=y&p=' . $id;
  }
  $dom = $testor->get_html( $url );
  $elements = $testor->find_by_class( $dom, 'wpdk-title' );
  $title = '';
  foreach ( $elements as $item ) {
    $title = $item->textContent;
    break;
  }
  $elements = $testor->find_by_class( $dom, 'wpdk-content' );
  $content = '';
  foreach ( $elements as $item ) {
    $content = $item->textContent;
    break;
  }
  $testor->contains( $title, "[$id]", "$id - title" );
  $testor->contains( $content, "[$id]", "$id - content" );
}

function test_main() {
  global $testor, $test_count, $test_start;
  $max_count = $test_count;
  $id = $test_start;
  do {
    test_with_id( $id );
    $id++;
  } while ( $id <= $max_count );

  echo $testor->export();
}

test_main();
?>
