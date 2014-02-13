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
   * @var int Tracks # of classes per file, which can't exceed 1.
   */
  protected $classCount = 0;

  /**
   * @var array Tracks files that were checked, so we only return errors once.
   */
  protected $filesChecked = array();

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
    // No need to run these sniffs more than once per file.
    $filename = $phpcsFile->getFilename();
    if (isset($this->filesChecked[$filename])) {
      return;
    }
    else {
      $this->filesChecked[$filename] = 1;
    }

    // Check for a "lib" folder in the filename. If we don't have one, this
    // is not in a PSR-* compliant location.

    if (!strstr($filename, '/lib/') || !strstr($filename, '/src/' /* Future-proofing */)) {
      // Is this a test? Those are special.
      $fileExtension = strtolower(substr($filename, -4));
      if ($fileExtension === 'test') {
        // @todo Convert this to a fixable error.
        $phpcsFile->addError('Test classes should be moved into a PSR-* compatible Tests directory: https://drupal.org/node/1543796', $stackPtr);
      }
      else {
        // @todo Convert this to a fixable error.
        $phpcsFile->addError('Classes should now moved to PSR-* standards: https://drupal.org/node/1320394', $stackPtr);
      }
    }

    // Make sure there's not more than one class defined per file.
    $tokens = $phpcsFile->getTokens();
    foreach ($tokens as $token) {
      if ($token['type'] == "T_CLASS") {
        $this->classCount++;
      }
    }
    if ($this->classCount > 1) {
      $phpcsFile->addError('PSR-* dictates only one class definition per file: https://drupal.org/node/1320394', $stackPtr);
    }
  }
}
