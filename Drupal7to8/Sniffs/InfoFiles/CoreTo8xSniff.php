<?php
/**
 * Drupal7to8_Sniffs_InfoFiles_CoreTo8xSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Verifies that YAML syntax in .info.yml is correct.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_InfoFiles_CoreTo8xSniff implements PHP_CodeSniffer_Sniff {

  /**
   * {@inheritdoc}
   */
  public function register() {
    // Fire on text outside of PHP.
    return array(T_INLINE_HTML);
  }

  /**
   * {@inheritdoc}
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
    // Only process on .info.yml files.
    $fileExtension = strtolower(substr($phpcsFile->getFilename(), -8));
    if ($fileExtension !== 'info.yml') {
      return;
    }

    // Only run once per file.
    $tokens = $phpcsFile->getTokens();
    if ($tokens[$stackPtr]['line'] !== 1) {
      return;
    }

    // Check for invalid "core" attribute.
    $file = file_get_contents($phpcsFile->getFilename());
    if (strstr($file, 'core: 7.x')) {
      $phpcsFile->addError('Upgrade core version to "8.x" in .info.yml file: https://drupal.org/node/1935708', $stackPtr, 'CoreTo8x');
    }
  }
}
