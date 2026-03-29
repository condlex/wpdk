<?php
global $wpdk_config;
require_once(__DIR__ . '/config.php');

function wpdk_current_post() {
  if (!wpdk_disk_available()) return false;
  $id = wpdk_current_post_id();
  if ($id <= 0) return false;
  $cp = wpdk_load_post($id);
  if ($cp === false) return false;
  return array('title' => $cp['title'], 'content' => $cp['content'], 'time' => $cp['time']);
}

function wpdk_current_post_id() {
  if (isset($_GET['p'])) {
    $id = intval($_GET['p']);
    if ($id > 0) return $id;
  }
  $site_url = site_url();
  $pos = strpos($site_url, "/", strlen("https://"));
  if ($pos !== false) {
    $site_url = substr($site_url, $pos);
    $url = $_SERVER['REQUEST_URI'];
    $url = substr($url, strlen($site_url));
    if (strpos($url, "/wpdk/") !== false) {
      $url = substr($url, strlen("/wpdk/"));
      $flds = explode("/", $url);
      return intval($flds[0]);
    }
  }
  return 0;
}

function wpdk_disk_available() {
  if (is_admin()) return false;
  $site_url = site_url();
  $pos = strpos($site_url, "/", strlen("https://"));
  if ($pos !== false) {
    $site_url = substr($site_url, $pos);
    $url = $_SERVER['REQUEST_URI'];
    $url = substr($url, strlen($site_url));
    if (strpos($url, "/wpdk/") !== false) {
      return true;
    }
  }
  if (!isset($_GET['wpdk'])) return false;
  if ($_GET['wpdk'] !== 'y') return false;
  return true;
}

function wpdk_post_slug($id, $title, $now) {
  $chars = array('a' => '_', 'b' => '_', 'c' => '_', 'd' => '_', 'e' => '_', 'f' => '_', 'g' => '_', 'h' => '_', 'i' => '_', 'j' => '_', 'k' => '_', 'l' => '_', 'm' => '_', 'n' => '_', 'o' => '_', 'p' => '_', 'q' => '_', 'r' => '_', 's' => '_', 't' => '_', 'u' => '_', 'v' => '_', 'w' => '_', 'x' => '_', 'y' => '_', 'z' => '_', '-' => '_', '_' => '_', '0' => '_', '1' => '_', '2' => '_', '3' => '_', '4' => '_', '5' => '_', '6' => '_', '7' => '_', '8' => '_', '9' => '_');
  $src = strtolower($title);
  $slug = [];
  $size = strlen($src);
  for ($i = 0; $i < $size; $i++) {
    $c = $src[$i];
    if (isset($chars[$c])) {
      $slug[] = $c;
    } else {
      $slug[] = '-';
    }
  }
  return '/' . $id . '/' . $now->format('Y/m/d') . '/' . implode('', $slug) . '.html';
}

function wpdk_save_post($post) {
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $post_dir = $data_dir . '/posts';
  $id = $post['id'];
  if ($id <= 0) {
    $id = wpdk_last_post_id() + 1;
  }
  date_default_timezone_set('UTC');
  $now = new DateTime();
  $post['time'] = $now->format('Y-m-d H:i:s.v');
  $post['slug'] = wpdk_post_slug($id, $post['title'], $now);
  $slug = wpdk_id_to_slug($id);
  $dir = $post_dir . '/' . $slug;
  $ser = wpdk_serialize($post);
  @mkdir(dirname($dir), 0777, true);
  wpdk_merge_save($dir, $id, $ser);
  $lid = wpdk_last_post_id();
  if ($id > $lid) {
    wpdk_last_post_id($id);
  }
  return $id;
}

function wpdk_merge_save($dir, $id, $content) {
  global $wpdk_config;
  $pcnt = $wpdk_config['pcnt'];
  $fn = $dir . '.wpdk';
  $fh = false;
  if (!is_file($fn)) {
    $fh = fopen($fn, "w+");
    fwrite($fh, 'wpdk@');
    $item = str_pad($id, 12, ' ') . '_' . str_pad((5 + 39 * $pcnt), 12, ' ') . '_' . str_pad(strlen($content), 12, ' ') . '|';
    fwrite($fh, $item);
    for ($i = 1; $i < $pcnt; $i++) {
      $item = str_pad(0, 12, ' ') . '_' . str_pad(0, 12, ' ') . '_' . str_pad(strlen($content), 12, ' ') . '|';
      fwrite($fh, $item);
    }
    fwrite($fh, $content . '@wpdk@');
    fclose($fh);
  } else {
    $fh = fopen($fn, "r+");
    $header = fread($fh, 5);
    if ($header !== 'wpdk@') {
      fclose($fh);
    } else {
      $fnd = false;
      $hidx = 0;
      $offset = 0;
      $osize = 0;
      $bidx = -1;
      for ($i = 0; $i < $pcnt; $i++) {
        $item = fread($fh, 39);
        $lines = explode("_", $item);
        $sig = intval($lines[0]);
        if ($sig == $id) {
          $fnd = true;
          $hidx = $i;
          $offset = intval($lines[1]);
          $osize = intval(str_replace('|', '', $lines[2]));
          break;
        } else if ($sig <= 0) {
          $bidx = $i;
          break;
        }
      }
      if (!$fnd) {
        fseek($fh, 0, SEEK_END);
        $fsz = ftell($fh);
        $item = str_pad($id, 12, ' ') . '_' . str_pad($fsz, 12, ' ') . '_' . str_pad(strlen($content), 12, ' ') . '|';
        fseek($fh, 5 + $bidx * 39, SEEK_SET);
        fwrite($fh, $item);
        fclose($fh);
        $fh = fopen($fn, "a+");
        fwrite($fh, $content . '@wpdk@');
        fclose($fh);
      } else {
        fseek($fh, $offset, SEEK_SET);
        fwrite($fh, str_pad($content, $osize, ' '));
        fclose($fh);
      }
    }
  }
}

function wpdk_merge_load($dir, $id) {
  global $wpdk_config;
  $pcnt = $wpdk_config['pcnt'];
  $fn = $dir . '.wpdk';
  $fh = false;
  if (!is_file($fn)) {
    return false;
  } else {
    $fh = fopen($fn, "r");
    $header = fread($fh, 5);
    if ($header !== 'wpdk@') {
      fclose($fh);
      return false;
    } else {
      $fnd = false;
      $hidx = 0;
      $offset = 0;
      $osize = 0;
      for ($i = 0; $i < $pcnt; $i++) {
        $item = fread($fh, 39);
        $lines = explode("_", $item);
        $sig = intval($lines[0]);
        if ($sig == $id) {
          $fnd = true;
          $hidx = $i;
          $offset = intval($lines[1]);
          $osize = intval(str_replace('|', '', $lines[2]));
          break;
        }
      }
      if (!$fnd) {
        fclose($fh);
        return false;
      }
      fseek($fh, $offset, SEEK_SET);
      $content = fread($fh, $osize);
      fclose($fh);
      return $content;
    }
  }
}

function wpdk_load_post($id) {
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $post_dir = $data_dir . '/posts';
  if ($id <= 0) {
    return false;
  }
  $slug = wpdk_id_to_slug($id);
  $dir = $post_dir . '/' . $slug;
  @mkdir(dirname($dir), 0777, true);
  $rs = wpdk_merge_load($dir, $id);
  if ($rs === false) return false;
  return wpdk_unserialize($rs);
}

function wpdk_delete_post($id) {
  return;
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $post_dir = $data_dir . '/posts';
  if ($id <= 0) {
    return false;
  }
  $slug = wpdk_id_to_slug($id);
  $dir = $post_dir . '/' . $slug;
  @mkdir($dir, 0777, true);
  $fn = $dir . '/' . $id . '.txt';
  unlink($fn);
  return true;
}

function wpdk_delete_all_posts() {
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $post_dir = $data_dir . '/posts';
  wpdk_del_tree($post_dir);
}

function wpdk_serialize($input) {
  return gzcompress(serialize($input), 9);
}

function wpdk_unserialize($input) {
  return unserialize(gzuncompress($input));
}

function wpdk_last_post_id($id = false) {
  global $wpdk_config;
  $data_dir = $wpdk_config['data_dir'];
  $post_dir = $data_dir . '/posts';
  @mkdir($post_dir, 0777, true);
  if ($id === false) {
    $id = intval(@file_get_contents($post_dir . '/last.txt'));
  } else {
    file_put_contents($post_dir . '/last.txt', $id . '');
  }
  return $id;
}

function wpdk_id_to_slug($id) {
  global $wpdk_config;
  $lvl = 5;
  $cnt = 100;
  if (isset($wpdk_config['pcnt'])) {
    $cnt = intval($wpdk_config['pcnt']);
    if ($cnt <= 0) $cnt = 100;
  }
  if (isset($wpdk_config['plvl'])) {
    $lvl = intval($wpdk_config['plvl']);
    if ($lvl <= 0) $lvl = 5;
  }
  $slug = [];
  $v = $id;
  for ($i = 0; $i < $lvl; $i++) {
    $j = $v % $cnt;
    if ($i > 0) {
      $slug[] = $j;
    }
    $v = ($v - $j) / $cnt;
  }
  return implode("/", array_reverse($slug));
}

function wpdk_last_slug($id) {
  global $wpdk_config;
  $cnt = 100;
  if (isset($wpdk_config['pcnt'])) {
    $cnt = intval($wpdk_config['pcnt']);
    if ($cnt <= 0) $cnt = 100;
  }
  return $id % $cnt;
}

function wpdk_del_tree($dir) {
  $files = array_diff(scandir($dir), array('.', '..'));
  foreach ($files as $file) {
    (is_dir("$dir/$file")) ? wpdk_del_tree("$dir/$file") : unlink("$dir/$file");
  }
  return rmdir($dir);
}

?>
