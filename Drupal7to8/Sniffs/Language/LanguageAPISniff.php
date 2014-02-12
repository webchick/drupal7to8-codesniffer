<?php
/**
 * Drupal7to8_Sniffs_Language_LanguageAPISniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Update to the new language API.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_Language_LanguageAPISniff extends Drupal7to8_Base_FunctionReplacementSniff {

    protected $message = '!function() has been removed in the new language API: https://drupal.org/node/1766152';

    protected $code = 'LanguageAPI';

    protected $forbiddenFunctions = array(
        'language' => '\Drupal::languageManager()->getCurrentLanguage',
        'language_list' => '\Drupal::languageManager()->getLanguages',
        'language_load' => '\Drupal::languageManager()->getLanguage',
        'language_default' => '\Drupal::languageManager()->getDefaultLanguage',
    );

}
