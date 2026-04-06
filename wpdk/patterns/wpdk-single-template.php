<?php
/*
 * Copyright (c) 2026 by Dinh Thoai Tran <zinospetrel@sdf.org>.
 * All rights reserved.
 *
 * Source code of 'Wordpress On Disk' (wpdk) theme is licensed under the GPLv2.
 *
 * Viewing copyright information at https://github.com/condlex/wpdk/LICENSE .
 */

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
