<?php
/**
 * Drupal7to8_Sniffs_Utility_TokenRange.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

class Drupal7to8_Utility_TokenRange {

  /**
   * Retrieves the content (string representation) for a range of tokens.
   *
   * @param array $tokens
   *   Array of tokens as returned by PHP_CodeSniffer_File::getTokens()
   * @param int $start
   * @param int $end
   */
  public static function getContent(array $tokens, $start = 0, $end = NULL) {
    $token_keys = array_keys($tokens);
    if(!$end) {
      $end = array_pop($token_keys);
    }
    if(!$start) {
      $start = array_shift($token_keys);
    }

    $content = '';
    for ($i = $start; $i <= $end; $i++) {
      $content .= $tokens[$i]['content'];
    }
    return $content;
  }

  /**
   * Removes a range of tokens.
   *
   * @param PHP_CodeSniffer_Fixer $fixer
   * @param int $start
   * @param int $end
   */
  public static function remove(PHP_CodeSniffer_Fixer $fixer, $start, $end) {
    for ($i = $start; $i <= $end; $i++) {
      $fixer->replaceToken($i, '');
    }
  }

  public static function tokenSubset(array $tokens, $start, $end) {
    for ($i = $start; $i <= $end; $i++) {
      $fixer->replaceToken($i, '');
    }
  }

}
