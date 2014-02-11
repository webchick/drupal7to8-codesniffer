<?php
/**
 * Drupal7to8_Sniffs_Cache_NewCacheApiSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Update to the new Cache API.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_Cache_NewCacheApiSniff extends Drupal7to8_Sniffs_Functions_FunctionReplacementSniff {

  protected $message = '!function() has been removed in the new Cache API: https://drupal.org/node/1766152';

  protected $code = 'NewCacheAPI';

  protected $forbiddenFunctions = array(
    'cache_get' => '\Drupal::cache()->get',
    'cache_set' => '\Drupal::cache()->set',
    // Ideally we'd automatically fix cache_clear_all() calls too, but in some
    // cases cache tags should be used, and it's impossible to automatically
    // determine which cache tags should be used.
    'cache_clear_all' => NULL,
  );

  protected $dynamicArgumentReplacements = array(
    'cache_get' => array(
      'arguments' => array(1),
      'string' => '\Drupal::cache($1)->get',
    ),
    'cache_set' => array(
      'arguments' => array(2),
      'string' => '\Drupal::cache($2)->set',
    ),
  );

}
