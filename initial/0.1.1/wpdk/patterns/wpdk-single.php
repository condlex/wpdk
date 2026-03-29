<?php
/**
 * Title: Single Post
 * Slug: wpdk/wpdk-single
 *
 * @package WordPress
 * @subpackage wpdk
 * @since WPDK 0.1.1
 */
$cp = wpdk_current_post();
$title = 'Post is not found!';
$content = '';
$time = '';
if ($cp !== false) {
  $title = $cp['title'];
  $content = $cp['content'];
  $time = $cp['time'];
}
?>
<div class="wpdk-title"><?php echo $title; ?></div>
<div class="wpdk-content"><?php echo $content; ?></div>
<div class="wpdk-time"><?php echo $time; ?></div>
