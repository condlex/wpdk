<?php
global $wpdk_config;

$wpdk_config = array(
  'build_uri' => 'http://localhost/wpdk-t/',
  'root_uri' => 'http://localhost/wpdk',
  'plvl' => 5,
  'pcnt' => 100,
  'merge' => true,
  'data_dir' => 'rsx/data'
);

$data_dir = __DIR__ . '/../wpdk-t/' . $wpdk_config['data_dir'];
mkdir($data_dir, 0777, true);
$wpdk_config['data_dir'] = $data_dir;
?>
