<?php
global $wpdk_config;
require_once __DIR__ . '/wpdk/config.php';

$script_file = $wpdk_config['root_uri'] . '/wp-content/themes/wpdk/wpdk-t/';
$max_count = 1000;
$max_count = 500;
$count = 1;
do {
  $url = $script_file . '?bs=10000&bm=' . $max_count . '&bn=' . $count . '&ph=5';
  $result = file_get_contents($url);
  echo '[R] ', $count, ' / ', $max_count, '\n', $result;
  $count++;
} while ( $count <= $max_count );

?>
