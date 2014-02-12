<?php
/**
 * Drupal7to8_Utility_CreateFile
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Utility class for writing out files.
 *
 * @todo Add methods for creating yaml files, PSR-N class files, etc.
 */
class Drupal7to8_Utility_CreateFile {

  /**
   * Replace a set of tokens in a boilerplate file template.
   *
   * @param string $boilerPath
   *   The path to the boilerplate file.
   * @param array $boilerTokens
   *   An associative array of tokens to replace in the boilerplate file,
   *   with the tokens as the keys and the values as the replacements
   *
   * @return string|null
   *   The PHP code with the tokens replaced, or NULL on failure.
   */
  public static function replaceTokens($boilerPath, array $boilerTokens) {
    if ($boilerplate = file_get_contents($boilerPath)) {
      return str_replace(array_keys($boilerTokens), array_values($boilerTokens), $boilerplate);
    }
  }

  /**
   * Converts an underscore-separated name to CamelCase.
   *
   * @param string $name
   *   An underscore-separated string (machine name, variable name, etc.).
   * @param bool $lowerCamel
   *   Boolean flag to convert to lowerCamel instead of UpperCamel. Defaults to
   *   FALSE.
   *
   * @return
   *   The converted string.
   */
  public static function camelUnderscores($name, $lowerCamel = FALSE) {
    $pieces = explode('_', $name);
    array_walk($pieces, 'strtolower');
    array_walk($pieces, 'ucfirst');
    if ($lowerCamel) {
      $pieces[0] = strtolower($pieces[0]);
    }
    return implode('', $pieces);
  }

}
