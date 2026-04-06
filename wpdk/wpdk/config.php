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

$wpdk_config = array(
  'build_uri' => 'http://localhost/wpdk-t/',
  'root_uri'  => 'http://localhost/wpdk',
  'plvl'      => 5,
  'pcnt'      => 200,
  'merge'     => true,
  'data_dir'  => '/bioogr/data/wpdk'
);

if ( $wpdk_config['data_dir'][0] !== '/' ) {
  $data_dir = __DIR__ . '/../wpdk-t/' . $wpdk_config['data_dir'];
  $wpdk_config['data_dir'] = $data_dir;
} else {
  $data_dir = $wpdk_config['data_dir'];
}
mkdir( $data_dir, 0777, true );
?>
