<?php
/*
 * Copyright (c) 2026 by Dinh Thoai Tran <zinospetrel@sdf.org>.
 * All rights reserved.
 *
 * Source code of 'Wordpress On Disk' (wpdk) theme is licensed under the GPLv2.
 *
 * Viewing copyright information at https://github.com/condlex/wpdk/LICENSE .
 */

global $wpdk_config, $wpdk_id_map;
require_once __DIR__ . '/config.php';
$wpdk_id_map = [];

function wpdk_admin_new_post() {
  global $wpdk_config;
  if ( $_GET['post_type'] !== 'postisk' ) return;
  $uri = $wpdk_config['root_uri'] . '/wp-admin/edit.php?post_type=postisk&edit=y&new=y';
  header('Location: ' . $uri);
  exit();
}

function wpdk_admin_edit_posts() {
  if ( $_GET['post_type'] !== 'postisk' ) return;
  require_once ABSPATH . 'wp-admin/admin-header.php';
  require_once __DIR__ . '/../patterns/wpdk-admin-search-template.php';
  exit();
}

function wpdk_setup_admin() {
	register_post_type(
		'postisk',
		array(
			'labels'                => array(
				'name_admin_bar' => _x( 'Postisk', 'add new from admin bar' ),
				'name'               => _x( 'Postisks', 'post type general name' ),
				'singular_name'      => _x( 'Postisk', 'post type singular name' ),
				'add_new'            => __( 'Add Postisk' ),
				'add_new_item'       => __( 'Add Postisk' ),
				'new_item'           => __( 'New Postisk' ),
				'edit_item'          => __( 'Edit Postisk' ),
				'view_item'          => __( 'View Postisk' ),
				'all_items'          => __( 'All Postisks' ),
				'search_items'       => __( 'Search Postisks' ),
				'not_found'          => __( 'No postisks found.' ),
				'not_found_in_trash' => __( 'No postisks found in Trash.' ),
			),
			'public'                => true,
			'publicly_queryable'    => false,
			'capability_type'       => array( 'edit_posts' ),
			'capabilities'          => array(
				// Meta Capabilities.
				'edit_post'              => 'edit_post',
				'read_post'              => 'read_post',
				'delete_post'            => 'delete_post',
				// Primitive Capabilities.
				'edit_posts'             => 'edit_theme_options',
				'edit_others_posts'      => 'edit_theme_options',
				'delete_posts'           => 'edit_theme_options',
				'publish_posts'          => 'edit_theme_options',
				'read_private_posts'     => 'edit_theme_options',
				'read'                   => 'read',
				'delete_private_posts'   => 'edit_theme_options',
				'delete_published_posts' => 'edit_theme_options',
				'delete_others_posts'    => 'edit_theme_options',
				'edit_private_posts'     => 'edit_theme_options',
				'edit_published_posts'   => 'edit_theme_options',
			),

			'map_meta_cap'          => true,
			'menu_position'         => 21,
			'menu_icon'             => 'dashicons-admin-page',
			'hierarchical'          => true,
			'rewrite'               => false,
			'query_var'             => false,
			'delete_with_user'      => true,
			'supports'              => array(
				'title',
			),
			'show_in_rest'          => true,
			'rest_base'             => 'menu-items',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

function wpdk_current_post() {
  if ( ! wpdk_disk_available() ) return false;
  $id = wpdk_current_post_id();
  if ( $id <= 0 ) return false;
  $post = wpdk_load_post( $id );
  if ( $post === false ) return false;
  return array( 'title' => $post['title'], 'content' => $post['content'], 'time' => $post['time'] );
}

function wpdk_current_post_id() {
  if ( isset( $_GET['p'] ) ) {
    $id = intval( $_GET['p'] );
    if ( $id > 0 ) return $id;
  } else if ( isset( $_GET['page_id'] ) ) {
    $id = intval( $_GET['page_id'] );
    if ( $id > 0 ) return $id;
  }
  $site_url = site_url();
  $position = strpos( $site_url, '/', strlen( 'https://' ) );
  if ( $position !== false ) {
    $site_url = substr( $site_url, $position );
    $url = $_SERVER['REQUEST_URI'];
    $url = substr( $url, strlen( $site_url ) );
    if ( strpos( $url, '/wpdk/' ) !== false ) {
      $url = substr( $url, strlen( '/wpdk/' ) );
      $fields = explode( '/', $url );
      return intval( $fields[0] );
    }
  }
  return 0;
}

function wpdk_disk_available() {
  if ( is_admin() ) return false;
  $site_url = site_url();
  $position = strpos( $site_url, '/', strlen( 'https://' ) );
  if ( $position !== false ) {
    $site_url = substr( $site_url, $position );
    $url = $_SERVER['REQUEST_URI'];
    $url = substr( $url, strlen( $site_url ) );
    if ( strpos( $url, '/wpdk/' ) !== false ) {
      return true;
    }
  }
  if ( ! isset( $_GET['wpdk'] ) ) return false;
  if ( $_GET['wpdk'] !== 'y' ) return false;
  return true;
}

function wpdk_post_slug( $id, $title, $now ) {
  $character_set = array(
                     'a' => '_', 
                     'b' => '_', 
                     'c' => '_', 
                     'd' => '_', 
                     'e' => '_', 
                     'f' => '_', 
                     'g' => '_', 
                     'h' => '_', 
                     'i' => '_', 
                     'j' => '_', 
                     'k' => '_', 
                     'l' => '_', 
                     'm' => '_', 
                     'n' => '_', 
                     'o' => '_', 
                     'p' => '_', 
                     'q' => '_', 
                     'r' => '_', 
                     's' => '_', 
                     't' => '_', 
                     'u' => '_', 
                     'v' => '_', 
                     'w' => '_', 
                     'x' => '_', 
                     'y' => '_', 
                     'z' => '_', 
                     '-' => '_', 
                     '_' => '_', 
                     '0' => '_', 
                     '1' => '_', 
                     '2' => '_', 
                     '3' => '_', 
                     '4' => '_', 
                     '5' => '_', 
                     '6' => '_', 
                     '7' => '_', 
                     '8' => '_', 
                     '9' => '_'
                   );
  $source = strtolower( $title );
  $slug = [];
  $size = strlen( $source );
  for ( $i = 0; $i < $size; $i++ ) {
    $character = $source[ $i ];
    if ( isset( $character_set[ $character ] ) ) {
      $slug[] = $character;
    } else {
      $slug[] = '-';
    }
  }
  return '/' . $id . '/' . $now->format('Y/m/d') . '/' . implode( '', $slug ) . '.html';
}

function wpdk_save_post($post) {
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $post_dir = $data_dir . '/posts';
  $id = $post['id'];
  if ( $id <= 0 ) {
    $id = wpdk_last_post_id() + 1;
  }
  date_default_timezone_set( 'UTC' );
  $now = new DateTime();
  $post['time'] = $now->format( 'Y-m-d H:i:s.v' );
  $post['slug'] = wpdk_post_slug( $id, $post['title'], $now );
  $slug = wpdk_id_to_slug( $id );
  $directory = $post_dir . '/' . $slug;
  $serialization = wpdk_serialize( $post );
  @mkdir( dirname( $directory ), 0777, true );
  wpdk_merge_save( $directory, $id, $serialization );
  $last_id = wpdk_last_post_id();
  if ( $id > $last_id ) {
    wpdk_last_post_id( $id );
  }
  return $id;
}

function wpdk_merge_save( $directory, $id, $content ) {
  global $wpdk_config;
  $file_count = $wpdk_config['pcnt'];
  $filename = $directory . '.wpdk';
  $handle = false;
  if ( ! is_file( $handle ) ) {
    $handle = fopen( $filename, 'w+' );
    fwrite( $handle, 'wpdk@' );
    $item = str_pad( $id, 12, ' ' ) . '_' . str_pad( ( 5 + 39 * $file_count ), 12, ' ' ) . '_' . str_pad( strlen( $content ), 12, ' ' ) . '|';
    fwrite( $handle, $item );
    for ( $i = 1; $i < $file_count; $i++ ) {
      $item = str_pad( 0, 12, ' ' ) . '_' . str_pad( 0, 12, ' ' ) . '_' . str_pad( strlen( $content ), 12, ' ' ) . '|';
      fwrite( $handle, $item );
    }
    fwrite( $handle, $content . '@wpdk@' );
    fclose( $handle );
  } else {
    $handle = fopen( $filename, 'r+' );
    $header = fread( $handle, 5 );
    if ( $header !== 'wpdk@' ) {
      fclose( $handle );
    } else {
      $found = false;
      $header_index = 0;
      $offset = 0;
      $header_size = 0;
      $data_index = -1;
      for ( $i = 0; $i < $file_count; $i++ ) {
        $item = fread( $handle, 39 );
        $lines = explode( '_', $item );
        $signal = intval( $lines[0] );
        if ( $signal == $id ) {
          $found = true;
          $header_index = $i;
          $offset = intval( $lines[1] );
          $header_size = intval( str_replace( '|', '', $lines[2] ) );
          break;
        } else if ( $signal <= 0 ) {
          $data_index = $i;
          break;
        }
      }
      if ( ! $found ) {
        fseek( $handle, 0, SEEK_END );
        $file_size = ftell( $handle );
        $item = str_pad( $id, 12, ' ' ) . '_' . str_pad( $file_size, 12, ' ' ) . '_' . str_pad( strlen( $content ), 12, ' ' ) . '|';
        fseek( $handle, 5 + $data_index * 39, SEEK_SET );
        fwrite( $handle, $item );
        fclose( $handle );
        $handle = fopen( $filename, 'a+' );
        fwrite( $handle, $content . '@wpdk@' );
        fclose( $handle );
      } else {
        fseek( $handle, $offset, SEEK_SET );
        fwrite( $handle, str_pad( $content, $header_size, ' ' ) );
        fclose( $handle );
      }
    }
  }
}

function wpdk_merge_load( $directory, $id ) {
  global $wpdk_config;
  $file_count = $wpdk_config['pcnt'];
  $filename = $directory . '.wpdk';
  $handle = false;
  if ( ! is_file( $filename ) ) {
    return false;
  } else {
    $handle = fopen( $filename, 'r' );
    $header = fread( $handle, 5 );
    if ( $header !== 'wpdk@' ) {
      fclose( $handle );
      return false;
    } else {
      $found = false;
      $header_index = 0;
      $offset = 0;
      $header_size = 0;
      for ( $i = 0; $i < $file_count; $i++ ) {
        $item = fread( $handle, 39 );
        $lines = explode( '_', $item );
        $signal = intval( $lines[0] );
        if ( $signal == $id ) {
          $found = true;
          $header_index = $i;
          $offset = intval( $lines[1] );
          $header_size = intval( str_replace( '|', '', $lines[2] ) );
          break;
        }
      }
      if ( !$found ) {
        fclose( $handle );
        return false;
      }
      fseek( $handle, $offset, SEEK_SET );
      $content = fread( $handle, $header_size );
      fclose( $handle );
      return $content;
    }
  }
}

function wpdk_load_post( $id ) {
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $post_dir = $data_dir . '/posts';
  if ( $id <= 0 ) {
    return false;
  }
  $slug = wpdk_id_to_slug( $id );
  $directory = $post_dir . '/' . $slug;
  @mkdir( dirname( $directory ), 0777, true );
  $result = wpdk_merge_load( $directory, $id );
  if ( $result === false ) return false;
  return wpdk_unserialize( $result );
}

function wpdk_delete_post( $id ) {
  return;
}

function wpdk_delete_all_posts() {
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $post_dir = $data_dir . '/posts';
  wpdk_del_tree( $post_dir );
}

function wpdk_serialize( $input ) {
  return gzcompress( serialize( $input ), 9 );
}

function wpdk_unserialize( $input ) {
  return unserialize( gzuncompress( $input ) );
}

function wpdk_last_post_id( $id = false ) {
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $post_dir = $data_dir . '/posts';
  @mkdir( $post_dir, 0777, true );
  if ( $id === false ) {
    $id = intval( @file_get_contents( $post_dir . '/last.txt' ) );
  } else {
    file_put_contents( $post_dir . '/last.txt', $id . '' );
  }
  return $id;
}

function wpdk_id_to_slug( $id ) {
  global $wpdk_config;
  $level = 5;
  $count = 100;
  if ( isset( $wpdk_config['pcnt'] ) ) {
    $count = intval( $wpdk_config['pcnt'] );
    if ( $count <= 0 ) $count = 100;
  }
  if ( isset( $wpdk_config['plvl'] ) ) {
    $level = intval( $wpdk_config['plvl'] );
    if ( $level <= 0 ) $level = 5;
  }
  $slug = [];
  $value = $id;
  for ( $i = 0; $i < $level; $i++ ) {
    $temporary = $value % $count;
    if ($temporary >= 0) {
      $slug[] = $temporary;
    }
    $value = ( $value - $temporary ) / $count;
  }
  return implode( '/', array_reverse( $slug ) );
}

function wpdk_last_slug( $id ) {
  global $wpdk_config;
  $count = 100;
  if ( isset( $wpdk_config['pcnt'] ) ) {
    $count = intval( $wpdk_config['pcnt'] );
    if ( $count <= 0 ) $count = 100;
  }
  return $id % $count;
}

function wpdk_del_tree( $directory ) {
  $file_list = array_diff( scandir( $directory ), array( '.', '..' ) );
  foreach ( $file_list as $file ) {
    ( is_dir( "$dir/$file" ) ) ? wpdk_del_tree( "$dir/$file" ) : unlink( "$dir/$file" );
  }
  return rmdir( $directory );
}

function wpdk_search_sync_id_map( &$id_map, $id ) {
  global $wpdk_config;
  if ( isset( $id_map[ $id ] ) ) return true;
  $irng = $wpdk_config['irng'];
  $pino = ( ( $id - 1 ) - ( ( $id - 1 ) % $irng ) ) / $irng;
  $ino = ( $id - ( $id % $irng ) ) / $irng;
  if ( $ino > $pino ) {
    $data_dir = $wpdk_config['data_dir'];
    $indexes_dir = $data_dir . '/indexes/id_map';
    @mkdir( $indexes_dir, 0777, true );
    if ( $pino >= 0 ) {
      $filename = $indexes_dir . "/$pino.lst";
      $text = implode( ',', array_keys( $id_map ) );
      file_put_contents( $filename, $text );
    }
    $filename = $indexes_dir . "/$ino.lst";
    $id_map = [];
    if ( file_exists( $filename ) ) {
      $keys = explode( ',', @file_get_contents( $filename ) );
      foreach ( $keys as $k ) {
        $id_map[ intval( $k ) ] = 1;
      }
    }
  }
  $id_map[ $id ] = 1;
  return false;
}

function wpdk_search_load_id_map( $id ) {
  global $wpdk_config, $wpdk_id_map;
  $irng = $wpdk_config['irng'];
  $ino = ( $id - ( $id % $irng ) ) / $irng;
  if ( $ino >= 0 ) {
    $data_dir = $wpdk_config['data_dir'];
    $indexes_dir = $data_dir . '/indexes/id_map';
    if ( ! is_dir( $indexes_dir ) ) {
      @mkdir( $indexes_dir, 0777, true );
    }
    $filename = $indexes_dir . "/$ino.lst";
    $wpdk_id_map = [];
    if ( file_exists( $filename ) ) {
      $keys = explode( ',', @file_get_contents( $filename ) );
      foreach ( $keys as $k ) {
        $wpdk_id_map[ intval( $k ) ] = 1;
      }
    }
  }
}

function wpdk_search_save_id_map( $id ) {
  global $wpdk_config, $wpdk_id_map;
  $irng = $wpdk_config['irng'];
  $ino = ( $id - ( $id % $irng ) ) / $irng;
  if ( $ino >= 0 ) {
    $data_dir = $wpdk_config['data_dir'];
    $indexes_dir = $data_dir . '/indexes/id_map';
    if ( ! is_dir( $indexes_dir ) ) {
      @mkdir( $indexes_dir, 0777, true );
    }
    $filename = $indexes_dir . "/$ino.lst";
    $text = implode( ',', array_keys( $wpdk_id_map ) );
    file_put_contents( $filename, $text );
  }
}

function wpdk_search_write_file( $filename, $number_array ) {
  $number_size = 6;
  $handle = fopen( $filename, 'w+' );
  $size = count( $number_array );
  $base_set = array(
    5 => 1,
    4 => 255,
    3 => 65025,
    2 => 16581375,
    1 => 4228250625,
    0 => 1078203909375
  );
  for ( $i = 0; $i < $size; $i++ ) {
    $value = $number_array[ $i ];
    $string = str_pad( '', $number_size, chr(1) );
    for ( $j = 0; $j < $number_size - 1; $j++ ) {
      $base = $base_set[ $j ];
      $digit = ( $value - ( $value % $base ) ) / 255;
      $value = $value - $digit * $base;
      $string[ $j ] = chr( $digit + 1 );
    }
    $string[ $number_size - 1 ] = chr( $value + 1 );
    fwrite( $handle, $string );
  }
  fclose( $handle );
}

function wpdk_search_read_file( $filename ) {
  if ( ! is_file( $filename ) ) return [];
  $target = [];
  $number_size = 6;
  $base_set = array(
    5 => 1,
    4 => 255,
    3 => 65025,
    2 => 16581375,
    1 => 4228250625,
    0 => 1078203909375
  );
  $handle = fopen( $filename, 'r' );
  while ( true ) {
    $string = fread( $handle, $number_size );
    if ( strlen( $string ) < $number_size ) break;
    $value = 0;
    for ( $i = 0; $i < $number_size; $i++ ) {
      $value = $value + $base_set[ $i ] * ( ord( $string[ $i ] ) - 1 );
    }
    $target[] = $value;
  }
  fclose( $handle );
  return $target;
}

function wpdk_search_match_file( $filename, $lookup ) {
  if ( ! is_file( $filename ) ) return [];
  $target = [];
  $number_size = 6;
  $base_set = array(
    5 => 1,
    4 => 255,
    3 => 65025,
    2 => 16581375,
    1 => 4228250625,
    0 => 1078203909375
  );
  $handle = fopen( $filename, 'r' );
  while ( ! feof( $handle ) ) {
    $string = fread( $handle, $number_size );
    if ( strlen( $string ) < $number_size ) break;
    $value = 0;
    for ( $i = 0; $i < $number_size; $i++ ) {
      $value = $value + $base_set[ $i ] * ( ord( $string[ $i ] ) - 1 );
    }
    if ( $lookup === $value ) {
      fclose( $handle );
      return false;
    }
    $target[] = $value;
  }
  fclose( $handle );
  return $target;
}

function wpdk_search_simple_query( $query ) {
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $indexes_dir = $data_dir . '/indexes';
  $word_list_r = explode( ' ', $query );
  $used_hash = [];
  $word_list = [];
  $duplicated = [];
  foreach ( $word_list_r as $word ) {
    $word = trim( $word );
    if ( $word === '' ) continue;
    $word = strtolower( $word );
    if ( isset( $duplicated[ $word ] ) ) continue;
    $word_list[] = $word;
    $duplicated[ $word ] = 1;
  }
  $base_count = count( $word_list );
  foreach ( $word_list as $word ) {
    $word = md5( $word );
    $slug = [];
    $part_size = 8;
    $part_value = $word;
    do {
      $slug_item = substr( $part_value, 0, $part_size );
      $slug[] = $slug_item;
      $part_value = substr( $part_value, $part_size + 1 );
    } while ( strlen( $part_value ) > $part_size );
    if ( strlen( $part_value ) > 0 ) {
      $slug[] = $part_value;
    }
    $slug = implode( '/', $slug );
    $filename = $indexes_dir . '/' . $slug . '.wpdk';
    $number_array = wpdk_search_read_file( $filename );
    $no = 0;
    do {
      foreach ( $number_array as $id ) {
        if ( isset( $used_hash[ $id ] ) ) {
          $used_hash[ $id ]++;
        } else {
          $used_hash[ $id ] = $base_count;
        }
      }
      $no++;
      $filename = $indexes_dir . '/' . $slug . '.' . $no . '.wpdk';
      $number_array = wpdk_search_read_file( $filename );
    } while ( count( $number_array ) > 0 );
  }
  arsort( $used_hash );
  return array_keys( $used_hash );
}

function wpdk_search_list_all() {
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $indexes_dir = $data_dir . '/indexes/id_map';
  if ( ! is_dir( $indexes_dir ) ) {
    @mkdir( $indexes_dir, 0777, true );
  }
  $file_list_r = glob( $indexes_dir . '/*.lst' );
  $file_list = [];
  foreach ( $file_list_r as $item ) {
    $value = intval( str_replace( '.lst', '', $item ) );
    $file_list[] = $value;
  }
  $max_count = max( $file_list );
  $max_count = ( $max_count + 1 ) * $wpdk_config['irng'];
  $result = [];
  for ( $id = 1; $id <= $max_count; $id++ ) {
    $result[] = $id;
  }
  return $result;
}

function wpdk_search_index_post( $post_id ) {
  global $wpdk_config, $wpdk_id_map;
  if ( wpdk_search_sync_id_map( $wpdk_id_map, $post_id ) ) return;
  $data_dir = $wpdk_config['data_dir'];
  $indexes_dir = $data_dir . '/indexes';
  $post = wpdk_load_post( $post_id );
  if ( $post === false ) return;
  $title = $post['title'];
  $content = $post['content'];
  $write_count = 0;
  $used_marker = [];
  wpdk_search_index_data( $indexes_dir, $title, $post_id, $write_count, $used_marker );
  wpdk_search_index_data( $indexes_dir, $content, $post_id, $write_count, $used_marker );
  echo "[I] ", $write_count, "\n";
}

function wpdk_search_index_data( $indexes_dir, $data, $post_id, &$write_count, &$used_marker ) {
  $maximum_number_count = 1024 * 4;
  $word_list_r = explode( ' ', $data );
  $word_list = [];
  $duplicated = [];
  foreach ( $word_list_r as $word ) {
    $word = trim( $word );
    if ( $word === '' ) continue;
    $word = strtolower( $word );
    if ( isset( $duplicated[ $word ] ) ) continue;
    $word_list[] = $word;
    $duplicated[ $word ] = 1;
  }
  foreach ( $word_list as $word_r ) {
    $word = md5( $word_r );
    if ( isset( $used_marker[ $word . '_' . $post_id ] ) ) continue;
    $used_marker[ $word . '_' . $post_id ] = 1;
    $slug = [];
    $part_size = 8;
    $part_value = $word;
    do {
      $slug_item = substr( $part_value, 0, $part_size );
      $slug[] = $slug_item;
      $part_value = substr( $part_value, $part_size + 1 );
    } while ( strlen( $part_value ) > $part_size );
    if ( strlen( $part_value ) > 0 ) {
      $slug[] = $part_value;
    }
    $slug = implode( '/', $slug );
    $filename = $indexes_dir . '/' . $slug . '.wpdk';
    if ( ! is_dir( dirname( $filename ) ) ) {
      @mkdir( dirname( $filename ), 0777, true );
    }
    $number_array = wpdk_search_match_file( $filename, $post_id );
    if ( $number_array === false ) {
      continue;
    }
    $no = 0;
    while ( count( $number_array ) > $maximum_number_count ) {
      $no++;
      $filename = $indexes_dir . '/' . $slug . '.' . $no . '.wpdk';
      $number_array = wpdk_search_match_file( $filename, $post_id );
      if ( $number_array === false ) {
        break;
      } else if ( count( $number_array ) == 0 ) {
        break;
      }
    }
    if ( $number_array === false ) continue;
    $number_array[] = $post_id;
    $write_count++;
    wpdk_search_write_file( $filename, $number_array );
  }
}

?>
