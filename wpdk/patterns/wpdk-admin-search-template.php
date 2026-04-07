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
 * Title: Search
 * Slug: wpdk/wpdk-admin-search-template
 *
 * @package WordPress
 * @subpackage wpdk
 * @since WPDK 0.1.1
 */

function get_param( $key ) {
  if ( isset( $_POST[ $key ] ) ) return $_POST[ $key ];
  if ( isset( $_GET[ $key ] ) ) return $_GET[ $key ];
  return '';
}

function summarize( $content, $summary_size ) {
  $summary = $content;
  if ( strlen( $summary ) < $summary_size ) {
    return $summary;
  }
  $position = strpos( $summary, ' ', $summary_size );
  $summary = substr( $summary, 0, $position );
  return $summary;
}

$available = true;

if ( $available ) {
  if ( get_param('edit') === 'y' ) {
    $available = false;
    require_once __DIR__ . '/wpdk-admin-edit-template.php';
  }
}

if ( $available ) {
  $summary_size = 500;
  $current_uri = $_SERVER['REQUEST_URI'];
  $position = strrpos( $current_uri, '?' );
  if ( $position !== false ) {
    $current_uri = substr( $current_uri, 0, $position );
  }
  $current_uri_normal = str_replace( 'wp-admin/edit.php', '', $current_uri );
  $query = trim( get_param( 'q' ) );

  $page_size = intval( get_param( 'ps' ) );
  if ( $page_size <= 0 ) {
    $page_size = 20;
  }
  $page_no = intval( get_param( 'pn' ) );
  if ( $page_no <= 0 ) {
    $page_no = 1;
  }
  $from_count = ( $page_no - 1 ) * $page_size;
  $to_count = ( $page_no ) * $page_size;

  $post_list = [];
  $searching = false;
  $page_list = [ 1 ];
  if ( strlen( $query ) > 0 ) {
    $searching = true;
    $id_list = wpdk_search_simple_query( $query );
    for ( $index = $from_count; $index < $to_count; $index++ ) {
      $id = $id_list[ $index ];
      $post = wpdk_load_post( $id );
      if ( $post === false ) continue;
      $summary = summarize( $post['content'], $summary_size );
      $post['summary'] = $summary;
      $post_list[] = $post;
    }
    $page_list = [];
    $post_count = count( $id_list );
    $post_count_less = $post_count - 1;
    $page_count = ( ( $post_count_less - ( $post_count_less % $page_size ) ) / $page_size ) + 1;
    for ( $no = 1; $no <= 5; $no++ ) {
      if ( $no < 1 || $no > $page_count ) continue;
      if ( in_array( $no, $page_list ) ) continue;
      $page_list[] = $no;
    }
    for ( $no = $page_no - 5; $no <= $page_no + 5; $no++ ) {
      if ( $no < 1 || $no > $page_count ) continue;
      if ( in_array( $no, $page_list ) ) continue;
      $page_list[] = $no;
    }
    for ( $no = $page_count - 5; $no <= $page_count; $no++ ) {
      if ( $no < 1 || $no > $page_count ) continue;
      if ( in_array( $no, $page_list ) ) continue;
      $page_list[] = $no;
    }
  }
}
?>
<?php
if ( $available ) {
?>


<div style="background-color: white; min-height: 800px; margin-left: -20px; margin-top: -10px; padding: 0px; padding-top: 10px; margin-bottom: -65px;">

<div class="wpdk-search-form">
  <form method="GET" action="<?php echo $current_uri; ?>">
    <input type="hidden" name="post_type" value="postisk" />
    <div class="wpdk-data"><input style="border-radius: 0px !important; border: dotted 1px gainsboro !important; font-family: monspace !important; font-size: 14px !important; line-height: 16px !important; min-height: 16px !important; padding: 2px 5px 2px 5px !important;" id="wpdk-query" name="q" type="text" class="wpdk-textbox" value="<?php echo $query; ?>" /></div>
    <div class="wpdk-toolbar" style="margin-top: 5px">
      <input type="submit" class="wpdk-button" value="Search" style="font-family: monospace !important; font-size: 12px !important;" />
    </div>
  </form>
</div>
<?php
  if ( $searching ) {
  ?>
    <div class="wpdk-search-list">
    <?php
      $index = 0;
      foreach ( $post_list as $post ) {
        $index++;
      ?>
        <div class="wpdk-search-item">
          <div class="wpdk-search-title">
            <?php echo ( $from_count + $index ); ?>.
            <a style="color: black" target="_blank" href="<?php echo $current_uri_normal . '?wpdk=y&p=' . $post['id']; ?>"><?php echo esc_html( $post['title'] ); ?></a>
          </div>
          <div class="wpdk-search-summary"><?php echo esc_html( $post['summary'] ); ?></div>
          <div class="wpdk-search-widget" style="margin-top: 5px">
            <a style="text-decoration: none; color: black;" class="wpdk-button" href="<?php echo $current_uri . '?post_type=postisk&edit=y&id=' . $post['id']; ?>">Edit</a>
          </div>
        </div>
      <?php
      }
    ?>
      <div class="wpdk-search-pager">
      <?php
        foreach ( $page_list as $page ) {
          $url = $current_uri . '?post_type=postisk&ps=' . $page_size . '&pn=' . $page . '&q=' . urlencode( $query );
          if ( $page == $page_no ) {
            ?>
            <a style="color: black;" class="wpdk-page-active" href="<?php echo $url; ?>"><?php echo $page; ?></a>
            <?php
          } else {
            ?>
            <a style="color: black;" class="wpdk-page-normal" href="<?php echo $url; ?>"><?php echo $page; ?></a>
            <?php
          }
        }
      ?>
      </div>
    </div>
  <?php
  }
?>

</div>

<?php
}
?>
