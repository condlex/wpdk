<?php
/*
 * Copyright (c) 2026 by Dinh Thoai Tran <zinospetrel@sdf.org>.
 * All rights reserved.
 *
 * Source code of 'class-testor.php' is licensed under the GPLv2.
 *
 * Viewing copyright information at https://github.com/condlex/testor/LICENSE .
 */

namespace testor;

class Testor {
  public function __construct( bool $verbose = false, bool $no_test = true, bool $no_time = true ) {
    $this->no_test = $no_test;
    $this->no_time = $no_time;
    $this->results = [];
    $this->verbose = $verbose;
    $this->timer = [];
  }

  public function contains( $operand, $value, string $key, string $message = "[__KEY__] case (contains) is __RESULT__\n__EVIDENT__\n" ) {
    if ( $this->no_test ) return;
    $position = strpos( $operand, $value );
    $this->test( $position !== false, $key, $message, "\nOperand: " . print_r( $operand, true ) . "\nValue: " . print_r( $value, true ) . "\n" );
  }

  public function not_contains( $operand, $value, string $key, string $message = "[__KEY__] case (not_contains) is __RESULT__\n__EVIDENT__\n" ) {
    if ( $this->no_test ) return;
    $position = strpos( $operand, $value );
    $this->test( $position === false, $key, $message, "\nOperand: " . print_r( $operand, true ) . "\nValue: " . print_r( $value, true ) . "\n" );
  }

  public function not_equals( $operand, $value, string $key, string $message = "[__KEY__] case (not_equals) is __RESULT__\n__EVIDENT__\n" ) {
    if ( $this->no_test ) return;
    $this->test( $operand !== $value, $key, $message, "\nOperand: " . print_r( $operand, true ) . "\nValue: " . print_r( $value, true ) . "\n" );
  }

  public function equals( $operand, $value, string $key, string $message = "[__KEY__] case (equals) is __RESULT__\n__EVIDENT__\n" ) {
    if ( $this->no_test ) return;
    $this->test( $operand === $value, $key, $message, "\nOperand: " . print_r( $operand, true ) . "\nValue: " . print_r( $value, true ) . "\n" );
  }

  public function greater_than( $operand, $value, string $key, string $message = "[__KEY__] case (greater_than) is __RESULT__\n__EVIDENT__\n" ) {
    if ( $this->no_test ) return;
    $this->test( $operand > $value, $key, $message, "\nOperand: " . print_r( $operand, true ) . "\nValue: " . print_r( $value, true ) . "\n" );
  }

  public function less_than( $operand, $value, string $key, string $message = "[__KEY__] case (less_than) is __RESULT__\n__EVIDENT__\n" ) {
    if ( $this->no_test ) return;
    $this->test( $operand > $value, $key, $message, "\nOperand: " . print_r( $operand, true ) . "\nValue: " . print_r( $value, true ) . "\n" );
  }

  public function test( bool $condition, string $key, string $message = "[__KEY__] case is __RESULT__\n__EVIDENT__\n", string $evident = '' ) {
    if ( $this->no_test ) return;
    $message = str_replace( '__KEY__', $key, $message );
    if ( $condition ) {
      $evident = '';
      $message = str_replace( '__RESULT__', 'success', $message );
      $message = str_replace( "\n__EVIDENT__\n", $evident, $message );
      if ( $this->verbose ) {
        echo "$message\n";
      }
      $this->results[ $key ] = array( 'success' => true, 'message' => $message );
    } else {
      $message = str_replace( '__RESULT__', 'failed', $message );
      $message = str_replace( "\n__EVIDENT__\n", $evident, $message );
      if ( $this->verbose ) {
        echo "$message\n";
      }
      $this->results[ $key ] = array( 'success' => false, 'message' => $message );
    }
  }

  public function export() : string {
    if ( $this->no_test && $this->no_time ) return "";
    $results = '';
    $errors = '';
    $timers = $this->time_export();
    $success_count = 0;
    $failed_count = 0;
    $case_count = 0;
    foreach ( $this->results as $key => $value ) {
      $case_count++;
      $message = $value['message'];
      if ( $value['success'] ) {
        $success_count++;
      } else {
        $errors .= "$message\n";
        $failed_count++;
      }
    }
    $results .= "\n=====>] Result [<=====\n" . "Success: $success_count, Failed: $failed_count" . "\n======================\n\n";
    $results .= "\n=====>] Errors [<=====\n" . $errors . "\n======================\n\n";
    $results .= "\n=====>] Timers [<=====\n" . $timers . "\n======================\n\n";
    return $results;
  }

  public function export_to_file( string $filename ) {
    if ( $this->no_test && $this->no_time ) return;
    file_put_contents( $filename, $this->export() );
  }

  public function time_clean() {
    if ( $this->no_time ) return;
    $this->timer = [];
  }

  public function time_export() : string {
    if ( $this->no_time ) return "";
    $text = '';
    foreach ( $this->timer as $key => $value ) {
      $time = ( $value['end'] - $value['start'] ) / ( 1000 * 1000 );
      $line = "\n$key : $time";
      $text .= $line;
    }
    return trim( $text );
  }

  public function time_start( string $key ) {
    if ( $this->no_time ) return;
    $time = microtime(true);
    if ( isset( $this->timer[ $key ] ) ) {
      $this->timer[ $key ]['start'] = $time;
    } else {
      $this->timer[ $key ] = array( 'start' => $time, 'end' => $time );
    }
  }

  function time_stop( string $key ) {
    if ( $this->no_time ) return;
    $time = microtime(true);
    if ( isset( $this->timer[ $key ] ) ) {
      $this->timer[ $key ]['end'] = $time;
    } else {
      $this->timer[ $key ] = array( 'start' => $time, 'end' => $time );
    }
  }

  public function &get_html( string $url ) {
    if ( $this->no_test ) return false;
    $html = file_get_contents( $url );
    $dom = new \DOMDocument();
    @$dom->loadHTML( $html, LIBXML_NOWARNING | LIBXML_NOERROR );
    return $dom;
  }

  public function find_by_class( \DOMDocument $dom, string $classname ) {
    if ( $this->no_test ) return false;
    $xpath = new \DOMXPath( $dom );
    $elements = $xpath->query( '/' . '/' . "*[contains(concat(' ', normalize-space(@class), ' '), ' " . $classname . " ')]" );
    return $elements;
  }
}
?>
