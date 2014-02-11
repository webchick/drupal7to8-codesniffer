<?php
/**
 * Drupal7to8_Sniffs_Classes_PsrMoveSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Handles moving class files around to match the PSR-0 and PSR-4 standards.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_Classes_PsrMoveSniff extends PHP_CodeSniffer_Sniff {

  /**
   * {@inheritdoc}
   */
  public function register()
  {
    // Fire on classes.
    return array(T_CLASS);
  }

  /**
   * {@inheritdoc}
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
  {
    // TODO! :)
  }
}
