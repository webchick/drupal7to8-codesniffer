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
class Drupal7to8_Sniffs_VariableAPI_VariableAPISniff implements PHP_CodeSniffer_Sniff {

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);

    }//end register()

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $variable_api = array('variable_get', 'variable_set', 'variable_del');
        if ($tokens[$stackPtr]['type'] == 'T_STRING' && in_array($tokens[$stackPtr]['content'], $variable_api)) {
            if (($tokens[$stackPtr+1]['type'] == T_WHITESPACE && $tokens[$stackPtr+2]['content'] == '(') || $tokens[$stackPtr+1]['content'] == '(') {
                // If we got this far, trigger an error.
                $fix = $phpcsFile->addFixableError($tokens[$stackPtr]['content'] . ' has been replaced by the Configuration API: https://drupal.org/node/2183531', $stackPtr, 'VariableAPI');
                if ($fix === true && $phpcsFile->fixer->enabled === true) {
                }
            }
        }
    }
}