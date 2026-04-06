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
require_once __DIR__ . '/config.php';

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

function wpdk_search_write_file( $filename, $number_array ) {
  $number_size = 6;
  $handle = fopen( $filename, 'w+' );
  $size = count( $number_array );
  for ( $i = 0; $i < $size; $i++ ) {
    $value = $number_array[ $i ];
    $string = str_pad( '', $number_size, '0' );
    for ( $j = 0; $j < $number_size; $j++ ) {
      $digit = $value % 256;
      $value = ( $value - $digit ) / 256;
      $string[ $number_size - 1 - $j ] = chr( $digit );
    }
    fwrite( $handle, $string );
  }
  fclose( $handle );
}

function wpdk_search_read_file( $filename ) {
  if ( ! is_file( $filename ) ) return [];
  $target = [];
  $number_size = 6;
  $handle = fopen( $filename, 'r' );
  while ( ! feof( $handle ) ) {
    $string = fread( $handle, $number_size );
    $value = 0;
    $base = 1;
    for ( $i = 0; $i < $number_size; $i++ ) {
      $value = $value + $base * ord( $string[ $number_size - 1 - $i ] );
      $base = $base * 256;
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
  $handle = fopen( $filename, 'r' );
  while ( ! feof( $handle ) ) {
    $string = fread( $handle, $number_size );
    if ( strlen( $string ) < $number_size ) break;
    $value = 0;
    $base = 1;
    for ( $i = 0; $i < $number_size; $i++ ) {
      $value = $value + $base * ord( $string[ $number_size - 1 - $i ] );
      $base = $base * 256;
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
  $word_list = explode( ' ', $query );
  $result = [];
  $used_hash = [];
  foreach ( $word_list as $word ) {
    $word = md5( strtolower( $word ) );
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
        if ( ! isset( $used_hash[ $id ] ) ) {
          $result[] = $id;
          $used_hash[ $id ] = 1;
        }
      }
      $no++;
      $filename = $indexes_dir . '/' . $slug . '.' . $no . '.wpdk';
      $number_array = wpdk_search_read_file( $filename );
    } while ( count( $number_array ) > 0 );
  }
  return $result;
}

function wpdk_search_index_post( $post_id ) {
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $indexes_dir = $data_dir . '/indexes';
  $post = wpdk_load_post( $post_id );
  if ( $post === false ) return;
  @mkdir( $indexes_dir, 0777, true );
  $title = $post['title'];
  $content = $post['content'];
  wpdk_search_index_data( $indexes_dir, $title, $post_id );
  wpdk_search_index_data( $indexes_dir, $content, $post_id );
}

function wpdk_search_index_data( $indexes_dir, $data, $post_id ) {
  $maximum_number_count = 1024 * 4;
  $word_list = explode( ' ', $data );
  $used_marker = [];
  foreach ( $word_list as $word ) {
    $word = md5( strtolower( $word ) );
    if ( isset( $used_marker[ $word ] ) ) continue;
    $used_marker[ $word ] = 1;
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
    @mkdir( dirname( $filename ), 0777, true );
    $number_array = wpdk_search_match_file( $filename, $post_id );
    if ( $number_array === false ) {
      echo "[F1] ", $post_id, " --| ", $filename, "\n";
      continue;
    }
    $no = 0;
    while ( count( $number_array ) > $maximum_number_count ) {
      $no++;
      $filename = $indexes_dir . '/' . $slug . '.' . $no . '.wpdk';
      $number_array = wpdk_search_match_file( $filename, $post_id );
      if ( $number_array === false ) {
        echo "[F2] ", $post_id, " --| ", $filename, "\n";
        break;
      } else if ( count( $number_array ) == 0 ) {
        echo "[F3] ", $post_id, " --| ", $filename, "\n";
        break;
      }
    }
    if ( $number_array === false ) continue;
    $number_array[] = $post_id;
    wpdk_search_write_file( $filename, $number_array );
  }
}

?>
