<?php

function &wpdku_html( $url ) {
  $html = file_get_contents( $url );
  $dom = new DOMDocument();
  @$dom->loadHTML( $html, LIBXML_NOWARNING | LIBXML_NOERROR );
  return $dom;
}

function wpdku_find_by_class( $dom, $classname ) {
  $xpath = new DOMXPath( $dom );
  $elements = $xpath->query( '/' . '/' . "*[contains(concat(' ', normalize-space(@class), ' '), ' " . $classname . " ')]" );
  return $elements;
}

?>
