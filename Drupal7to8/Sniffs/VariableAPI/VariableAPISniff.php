<?php
/**
 * Drupal7to8_Sniffs_VariableAPI_VariableAPISniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Handles variable_get(), variable_set() and variable_del().
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_VariableAPI_VariableAPISniff extends Drupal7to8_Sniffs_Functions_FunctionReplacementSniff {

  protected $message = '!function() has been replaced by the Configuration API: https://drupal.org/node/2183531';

  protected $code = 'VariableAPI';

  protected $forbiddenFunctions = array(
    'variable_get' => NULL,
    'variable_set' => NULL,
    'variable_del' => NULL,
  );

  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
    parent::process($phpcsFile, $stackPtr);
    $module_name = $this->getModuleName($phpcsFile);
  }

  /**
   * Determine the module name based on the file being examined.
   *
   * @param PHP_CodeSniffer_File $phpcsFile
   *   The code sniffer file.
   * @return string|null
   *   The module name if it can be determined, NULL if it cannot.
   */
  public function getModuleName(PHP_CodeSniffer_File $phpcsFile) {
    $file_parts = explode(DIRECTORY_SEPARATOR, $phpcsFile->getFilename());
    // Ignore the filename as we are traversing directories.
    array_pop($file_parts);

    // Check each directory path for the base .module file.
    while (count($file_parts) > 0) {
      $path = implode(DIRECTORY_SEPARATOR, $file_parts);
      $files = glob($path . DIRECTORY_SEPARATOR . '*.module');
      if (count($files) == 0) {
        // No module found, so search the parent directory.
        array_pop($file_parts);
        continue;
      }
      return basename($files[0], '.module');
    }

    return NULL;
  }
}
