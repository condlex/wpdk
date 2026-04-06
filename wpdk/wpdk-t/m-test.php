<?php
/*
 * Copyright (c) 2026 by Dinh Thoai Tran <zinospetrel@sdf.org>.
 * All rights reserved.
 *
 * Source code of 'Wordpress On Disk' (wpdk) theme is licensed under the GPLv2.
 *
 * Viewing copyright information at https://github.com/condlex/wpdk/LICENSE .
 */

global $wpdk_config, $testor_set, $root_uri, $test_count, $friendly, $g_result_dir, $g_used_threads;
require_once __DIR__ . '/wpdk/config.php';
require_once __DIR__ . '/wpdk/class-testor.php';

$g_used_threads = array();

$g_result_dir = '/bioogr/data/wpdk/m-test';
$friendly = false;
$test_count = 1000000;
$test_start = 1;
$user_count = 1000;

$user_count = 10;
$test_count = 100;

$root_uri = $wpdk_config['root_uri'] . '/';

$verbose = false;
$no_test = false;
$no_time = false;

$testor_set = [];
$testor_set['_'] = new \testor\Testor( $verbose, $no_test, $no_time );

function create_testor( $user_id ) {
  global $testor_set, $verbose, $no_test, $no_time;
  $testor_set[ $user_id ] = new \testor\Testor( $verbose, $no_test, $no_time );
}

function test_with_id( $user_id, $id ) {
  global $testor_set, $root_uri, $friendly;

  $testor = $testor_set[ $user_id ];

  $testor->time_start( "$user_id -- $id" );

  if ( $friendly ) {
    $url = $root_uri . 'wpdk/' . $id . '/';
  } else {
    $url = $root_uri . '?wpdk=y&p=' . $id;
  }

  $testor->time_start( "$user_id -- $id - html" );

  $dom = $testor->get_html( $url );
  $testor->time_stop( "$user_id -- $id - html" );
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

  $testor->contains( $title, "[$id]", "$user_id - $id - title" );
  $testor->contains( $content, "[$id]", "$user_id - $id - content" );

  $testor->time_stop( "$user_id -- $id" );
}

function test_user( $result_dir, $user_id ) {
  global $testor_set, $test_count, $test_start, $user_count;

  create_testor( $user_id );
  $testor = $testor_set[ $user_id ];

  $testor->time_start( "$user_id" );
  $max_count = $test_count;
  $id = $test_start;
  do {
    test_with_id( $user_id, $id );
    $id++;
  } while ( $id <= $max_count );

  $testor->time_stop( "$user_id" );
  $testor->export_to_file( $result_dir . '/' . $user_id . '.txt' );
}

class User_Test_Thread {
  function __construct( $result_dir, $user_id ) {
    $this->result_dir = $result_dir;
    $this->user_id = $user_id;
  }

  function run() {
    test_user( $this->result_dir, $this->user_id );
  }
}

function test_main() {
  global $g_result_dir, $user_count, $g_used_threads, $test_start, $test_count;
  $result_dir = $g_result_dir . '/' . $user_count . '.' . $test_start . '.' . $test_count;
  @shell_exec( "rm -rf $result_dir" );
  @mkdir( $result_dir, 0777, true );
  @shell_exec( "free -m > $result_dir/mem-a.txt" );

  for ( $user_id = 1; $user_id <= $user_count; $user_id++ ) {
    $pid = pcntl_fork();
    if ( $pid ) {
      $thread = new User_Test_Thread( $result_dir, $user_id );
      $thread->run();
      exit(0);
    }
  }

  @shell_exec( "free -m > $result_dir/mem-b.txt" );
}

test_main();
?>
