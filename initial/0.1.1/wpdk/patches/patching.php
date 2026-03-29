<?php

function wpdk_theme_patching() {
  $pc_dir = __DIR__ . '/wp-includes';
  $key = file_get_contents($pc_dir . '/class-wp.php.key');
  $repl = file_get_contents($pc_dir . '/class-wp.php.repl');
  $sc_file = __DIR__ . '/../../../../wp-includes/class-wp.php';
  $text = file_get_contents($sc_file);
  $pos = strpos($text, $repl);
  if ($pos !== false) {
    echo "[Patching] Patches has already been done!", "\n";
    return;
  }
  $pos = strpos($text, $key);
  if ($pos === false) {
    echo "[Patching] Key does not found!", "\n";
    return;
  }
  $text = substr($text, 0, $pos) . $repl . substr($text, $pos + strlen($key));
  file_put_contents($sc_file, $text);
  echo "[Patching] Patches are done!", "\n";
}

wpdk_theme_patching();
