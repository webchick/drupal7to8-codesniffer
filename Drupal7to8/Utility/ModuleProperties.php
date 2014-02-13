<?php
/**
 * Drupal7to8_Sniffs_Utility_ModuleProperties.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

class Drupal7to8_Utility_ModuleProperties {

  /**
   * Determine the module name for the file being examined.
   *
   * @param PHP_CodeSniffer_File $phpcsFile
   *   The code sniffer file.
   *
   * @return string|null
   *   The module name if it can be determined, NULL if it cannot.
   */
  static public function getModuleName(PHP_CodeSniffer_File $phpcsFile) {
    $module_properties = self::getModuleNameAndPath($phpcsFile);
    return $module_properties['module_name'];
  }

  /**
   * Determine the module directory path for the file being examined.
   *
   * @param PHP_CodeSniffer_File $phpcsFile
   *   The code sniffer file.
   *
   * @return string|null
   *   The module name if it can be determined, NULL if it cannot.
   */
  static public function getModulePath(PHP_CodeSniffer_File $phpcsFile) {
    $module_properties = self::getModuleNameAndPath($phpcsFile);
    return $module_properties['module_path'];
  }

  /**
   * Determine the module name and module directory path for the file.
   *
   * @param PHP_CodeSniffer_File $phpcsFile
   *   The code sniffer file.
   *
   * @return array
   *   An array containing:
   *   - module_name: The name of the module.
   *   - module_path: The path to the module directory.
   */
  static public function getModuleNameAndPath(PHP_CodeSniffer_File $phpcsFile) {
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
      $return['module_name'] = basename($files[0], '.module');
      $return['module_path'] = implode(DIRECTORY_SEPARATOR, $file_parts);
      break;
    }

    return $return;
  }

}
