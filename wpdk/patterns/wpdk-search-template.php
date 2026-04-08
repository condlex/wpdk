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
 * Slug: wpdk/wpdk-search-template
 *
 * @package WordPress
 * @subpackage wpdk
 * @since WPDK 0.1.1
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

function get_param( $key ) {
  if ( isset( $_POST[ $key ] ) ) return $_POST[ $key ];
  if ( isset( $_GET[ $key ] ) ) return $_GET[ $key ];
  return '';
}

function marking_title( $title, $word_list ) {
  foreach ( $word_list as $word ) {
    $word_size = strlen( $word );
    $n_title = '';
    $start = 0;
    $position = stripos( $title, $word, $start );
    while ( $position !== false ) {
      $n_title .= substr( $title, $start, $position - $start ) . '<span class="wpdk-mark" style="color: red">' . substr( $title, $position, $word_size ) . '</span>';
      $start = $position + $word_size;
      $position = stripos( $title, $word, $start );
    }
    $n_title .= substr( $title, $start );
    $title = $n_title;
  }
  return $title;
}

function marking_content( $content, $word_list, $context_size, $max_context_count = 3 ) {
  $start = 0;
  $context_count = 0;
  $new_content = '';
  $content_size = strlen( $content );
  $position = strlen( $content );
  $position_word = '';
  foreach ( $word_list as $word ) {
    $new_position = stripos( $content, $word, $start );
    if ( $new_position !== false ) {
      if ( $new_position < $position ) {
        $position = $new_position;
        $position_word = $word;
      }
    }
  }
  while ( $position_word !== '' && $context_count < $max_context_count ) {
    $context_count++;
    $begin = $position - $context_size;
    if ( $begin <= $start ) {
      $begin = $start;
    }
    $new_position = strpos( $content, ' ', $begin );
    if ( $new_position !== false ) {
      $begin = $new_position;
    }

    $end = $position + $context_size;
    if ( $end >= $content_size - 1 ) {
      $end = $content_size - 1;
    }
    $new_position = strpos( $content, ' ', $end - 10 );
    if ( $new_position !== false ) {
      $end = $new_position;
    }
    $new_content .= '<div class="wpdk-cutting" style="padding-top: 5px; padding-bottom: 5px; border-bottom: dotted 1px gainsboro;">' . marking_title( substr( $content, $begin, $end - $begin + 1 ), $word_list ) . '</div>';
    $start = $end;

    $position = strlen( $content );
    $position_word = '';
    foreach ( $word_list as $word ) {
      $new_position = stripos( $content, $word, $start );
      if ( $new_position !== false ) {
        if ( $new_position < $position ) {
          $position = $new_position;
          $position_word = $word;
        }
      }
    }
  }
  return $new_content;
}

$available = true;
if ( get_param('wpdk') === 'y' ) {
  $available = false;
  require_once __DIR__ . '/wpdk-single-template.php';
}

if ( $available ) {
  if ( get_param( 'random' ) === 'y' ) {
    $available = true;
    $id = rand(1, wpdk_last_post_id() );
    $current_uri = $_SERVER['REQUEST_URI'];
    $position = strrpos( $current_uri, '/' );
    if ( $position !== false ) {
      $current_uri = substr( $current_uri, 0, $position );
    }
    $current_uri .= '?p=' . $id . '&wpdk=y';
    header("Location: $current_uri");
    exit();
  }
}

if ( $available ) {
  $context_size = 100;
  $current_uri = $_SERVER['REQUEST_URI'];
  $position = strrpos( $current_uri, '/' );
  if ( $position !== false ) {
    $current_uri = substr( $current_uri, 0, $position );
  }
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
  if ( strlen( $query ) === 0 ) {
    $searching = true;
    $content_size = 300;
    $id_list = wpdk_search_list_all();
    for ( $index = $from_count; $index < $to_count; $index++ ) {
      $id = $id_list[ $index ];
      $post = wpdk_load_post( $id );
      if ( $post === false ) continue;
      $content = $post['content'];
      if ( strlen( $content ) > $content_size ) {
        $position = strpos( $content, ' ', $content_size - 10 );
        if ( $position === false ) {
          $content = substr( $content, 0, $content_size );
        } else {
          $content = substr( $content, 0, $position );
        }
      }
      $post['content'] = $content;
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
  } else {
    $word_list_r = explode( ' ', $query );
    $word_list = [];
    foreach ( $word_list_r as $word ) {
      $word = trim( $word );
      if ( $word === '' ) continue;
      $word_list[] = strtolower( $word );
    }
    $searching = true;
    $id_list = wpdk_search_simple_query( $query );
    for ( $index = $from_count; $index < $to_count; $index++ ) {
      $id = $id_list[ $index ];
      $post = wpdk_load_post( $id );
      if ( $post === false ) continue;
      $content = marking_content( esc_html( $post['content'] ), $word_list, $context_size );
      $post['content'] = $content;
      $title = marking_title( esc_html( $post['title'] ), $word_list );
      $post['title'] = $title;
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

<script>
function wpdk_random() {
  let url = '<?php echo $current_uri; ?>?random=y';
  window.open( url, '_blank' );
}

function wpdk_go() {
  let id = document.getElementById('wpdk-query').value;
  let url = '<?php echo $current_uri; ?>?p=' + id + '&wpdk=y';
  window.open( url, '_blank' );
}
</script>
<div class="wpdk-search-form">
  <form method="GET" action="<?php echo $current_uri; ?>">
    <div class="wpdk-data"><input id="wpdk-query" name="q" type="text" class="wpdk-textbox" value="<?php echo $query; ?>" /></div>
    <div class="wpdk-toolbar">
      <input type="submit" class="wpdk-button" value="Search" />
      <input type="button" class="wpdk-button" value="Jump" onclick="wpdk_go()" />
      <input type="button" class="wpdk-button" value="Random" onclick="wpdk_random()" />
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
            <a target="_blank" href="<?php echo $current_uri . '?wpdk=y&p=' . $post['id']; ?>"><?php echo $post['title']; ?></a>
          </div>
          <div class="wpdk-search-summary"><?php echo $post['content']; ?></div>
        </div>
      <?php
      }
    ?>
      <div class="wpdk-search-pager">
      <?php
        foreach ( $page_list as $page ) {
          $url = $current_uri . '?ps=' . $page_size . '&pn=' . $page . '&q=' . urlencode( $query );
          if ( $page == $page_no ) {
            ?>
            <a class="wpdk-page-active" href="<?php echo $url; ?>"><?php echo $page; ?></a>
            <?php
          } else {
            ?>
            <a class="wpdk-page-normal" href="<?php echo $url; ?>"><?php echo $page; ?></a>
            <?php
          }
        }
      ?>
      </div>
    </div>
  <?php
  }
?>

<?php
}
?>
