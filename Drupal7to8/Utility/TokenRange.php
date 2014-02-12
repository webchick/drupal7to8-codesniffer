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
  public static function getContent(array $tokens, $start, $end) {
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

}
