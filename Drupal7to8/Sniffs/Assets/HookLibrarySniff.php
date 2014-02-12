<?php
/**
 * Drupal7to8_Sniffs_Assets_HookLibrarySniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * hook_library() and hook_library_alter() have been renamed to
 * hook_library_info() and hook_library_info_alter().
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_Assets_HookLibrarySniff extends Drupal7to8_Base_HookImplementationChangeSniff {

  protected $hook = 'library';

  protected $has_alter_hook = TRUE;

  protected $message = 'hook_library_info() has been renamed: https://drupal.org/node/1294416';

  protected $code = 'HookLibrary';

  protected $is_fixable = TRUE;

  protected $renamed_hook = 'library_info';

}
