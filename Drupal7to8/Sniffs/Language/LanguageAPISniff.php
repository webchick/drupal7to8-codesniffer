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

    /**
     * {@inheritdoc}
     */
    protected function addError($phpcsFile, $stackPtr, $function, $pattern = NULL) {
        if ($function == 'language_list') {
            // Find the token range representing the nth argument.
            $result = $this->findNthArgument($phpcsFile, $stackPtr, 0);
            // Fall back on parent behavior if there is no nth argument.
            if ($result === FALSE) {
                parent::addError($phpcsFile, $stackPtr, $function, $pattern);
                return;
            }
            var_dump($result);
            $customMessage = 'The argument for the replacement of language_list(), languageManager()->getLanguages() does not take field names anymore. It takes language state. Review Language::STATE_* constants.';
            $fix = $phpcsFile->addFixableError($customMessage, $stackPtr, $this->code);
            if ($fix === TRUE && $phpcsFile->fixer->enabled === TRUE) {
                $this->insertFixMeComment($phpcsFile, $stackPtr, $customMessage, $this->forbiddenFunctions[$function]);
            }
        }
    }

}
