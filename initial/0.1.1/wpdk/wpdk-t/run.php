<?php
global $wpdk_config;
require_once(__DIR__ . '/wpdk/config.php');

$g_script_file = $wpdk_config['root_uri'] . '/wp-content/themes/wpdk/wpdk-t/';
$max_cnt = 1000;
$max_cnt = 10;
$cnt = 1;
do {
  $url = $g_script_file . '?bs=10000&bm=' . $max_cnt . '&bn=' . $cnt . '&ph=5';
  $rs = file_get_contents($url);
  echo '[R] ', $cnt, ' / ', $max_cnt, "\n", $rs;
  $cnt++;
} while ($cnt <= $max_cnt);

?>
