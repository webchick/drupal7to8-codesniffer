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
class Drupal7to8_Sniffs_Classes_PsrMoveSniff implements PHP_CodeSniffer_Sniff {

  /**
   * @var Tracks # of classes per file, which can't exceed 1.
   */
  protected $classCount = array();

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
    // Increment our class counter for this file.
    $this->classCount[$phpcsFile->getFilename()]++;

    // Check for a "lib" folder in the filename. If we don't have one, this
    // is not PSR-* compliant.
    if (!strstr($phpcsFile->getFilename(), '/lib/') || !strstr($phpcsFile->getFilename(), '/src/' /* Future-proofing */)) {
      $phpcsFile->addError('Classes should now moved to PSR-* standards: https://drupal.org/node/1320394');
    }

    // Make sure there is not more than one class in a file.
    if ($this->classCount[$phpcsFile->getFilename()] > 1) {
      $phpcsFile->addError('PSR-* dictates only one class definition per file: https://drupal.org/node/1320394');
    }
  }
}
