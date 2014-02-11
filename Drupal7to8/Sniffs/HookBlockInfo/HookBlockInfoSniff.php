<?php
/**
 * Drupal7to8_Sniffs_HookBlockInfo_HookBlockInfo.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Warns that .info files are now .info.yml files.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_HookBlockInfo_HookBlockInfoSniff implements PHP_CodeSniffer_Sniff {

  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register()
  {
      return array(T_FUNCTION);

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

      if ($tokens[$stackPtr]['type'] == 'T_FUNCTION' && strstr($tokens[$stackPtr+2]['content'], '_block_info')) {
        // If we got this far, trigger an error.
        $fix = $phpcsFile->addFixableError('hook_block_info() has been replaced by the Block Plugin API: https://drupal.org/node/1880620', $stackPtr, 'HookBlockInfo');
        if ($fix === true && $phpcsFile->fixer->enabled === true) {
        }
      }
  }
}