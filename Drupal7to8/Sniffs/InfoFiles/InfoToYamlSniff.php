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
 * Warns that .info files are now .info.yml files, and attempts to rename them.
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

    // If .info.yml file already exists, our work here is done.
    if (file_exists($phpcsFile->getFilename() . '.yml')) {
      return;
    }

    // If we got this far, trigger an error.
    $fix = $phpcsFile->addFixableError('.info files are now .info.yml files: https://drupal.org/node/1935708', $stackPtr, 'InfoToYaml');
    if ($fix === true && $phpcsFile->fixer->enabled === true) {
      // Take contents of the old file and write them out to an .info.yml file.
      $contents = file_get_contents($phpcsFile->getFilename());
      $filename = $phpcsFile->getFilename() . '.yml';
      file_put_contents($filename, $contents);

      // @todo Leave a @todo in the original .info file to remove it.
      //$contents = "; @todo: Remove this file once your module is ported.\n" . $contents;
      //file_put_contents($phpcsFile->getFilename(), $contents);
    }
  }
}
