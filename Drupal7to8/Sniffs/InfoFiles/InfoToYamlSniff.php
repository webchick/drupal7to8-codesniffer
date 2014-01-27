<?php
/**
 * Drupal7to8_Sniffs_InfoFiles_InfoToYamlSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Warns that .info files are now .info.yml files.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_InfoFiles_InfoToYamlSniff implements PHP_CodeSniffer_Sniff {

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
    // Only process on .info files.
    $fileExtension = strtolower(substr($phpcsFile->getFilename(), -4));
    if ($fileExtension !== 'info') {
      return;
    }

    // Only run once per file.
    $tokens = $phpcsFile->getTokens();
    if ($tokens[$stackPtr]['line'] !== 1) {
      return;
    }

    // If we got this far, trigger an error.
    $fix = $phpcsFile->addFixableError('.info files are now .info.yml files: https://drupal.org/node/1935708', $stackPtr, 'InfoToYaml');
    if ($fix === true && $phpcsFile->fixer->enabled === true) {
      // Take contents of the old file and write them out to an .info.yml file.
      $contents = file_get_contents($phpcsFile->getFilename());
      $filename = $phpcsFile->getFilename() . '.yml';
      file_put_contents($filename, $contents);

      // Remove the old file.
      // @todo This is not only dangerous, it also causes an error when the file
      // it was checking suddenly vanishes. ;)
      //unlink($phpcsFile->getFilename());
    }
  }
}
