<?php
/**
 * Title: Single Post
 * Slug: wpdk/wpdk-single-template
 *
 * @package WordPress
 * @subpackage wpdk
 * @since WPDK 0.1.1
 */
$id = wpdk_current_post_id();
$current_post = wpdk_current_post();
$title = 'Post is not found!';
$content = '';
$time = '';
if ( $current_post !== false ) {
  $title = $current_post['title'];
  $content = $current_post['content'];
  $time = $current_post['time'];
}
?>
<div class="wpdk-title"><?php echo $title; ?></div>
<div class="wpdk-content"><?php echo $content; ?></div>
<div class="wpdk-time"><?php echo $time; ?></div>
