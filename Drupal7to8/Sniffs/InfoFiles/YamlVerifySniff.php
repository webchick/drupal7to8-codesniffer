<?php
/**
 * Drupal7to8_Sniffs_InfoFiles_YamlVerifySniff.
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
class Drupal7to8_Sniffs_InfoFiles_YamlVerifySniff implements PHP_CodeSniffer_Sniff {

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

    // Verify that contents of file is valid YAML.
    $file = file_get_contents($phpcsFile->getFilename());
    // @todo: This should be much fancier and use e.g. Symfony YAML component.
    if (strstr($file, ' = ')) {
      $phpcsFile->addError('.info.yml file did not parse as valid YAML: https://drupal.org/node/1935708', $stackPtr, 'YamlVerify');
    }
  }
}
