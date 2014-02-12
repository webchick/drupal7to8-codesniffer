<?php
/**
 * Drupal7to8_Sniffs_File_FileCreateHtaccessSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * file_create_htaccess() renamed to to file_save_htaccess().
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_File_FileCreateHtaccessSniff extends Drupal7to8_Sniffs_Functions_FunctionReplacementSniff {

  protected $message = 'file_create_htaccess() renamed to to file_save_htaccess(): https://drupal.org/node/1336568';

  protected $code = 'FileCreateHtaccess';

  protected $forbiddenFunctions = array(
    'file_create_htaccess' => 'file_save_htaccess',
  );

}
