<?php
/**
 * Drupal7to8_Sniffs_HookMenu_HookMenuToD8.
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
class Drupal7to8_Sniffs_HookMenu_HookMenuToD8Sniff implements PHP_CodeSniffer_Sniff {

  protected $functionStart = 0;
  protected $functionStop = 0;
  protected $array_parent = FALSE;
  protected $return_var = '';
  protected $menu = array();
  protected $menu_function_whitelist = array('drupal_get_path', 't');

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
      $filename_info = pathinfo($phpcsFile->getFilename());


      if ($tokens[$stackPtr]['type'] == 'T_FUNCTION' &&
         ($tokens[$stackPtr+2]['content'] == Drupal7to8_Utility_ModuleProperties::getModuleName($phpcsFile) . '_menu' || $tokens[$stackPtr+2]['content'] == 'hook_menu')) {

        $this->functionStart  = $tokens[$stackPtr]['scope_opener'];
        $this->functionStop = $tokens[$stackPtr]['scope_closer'];
        $tokens = array_slice($tokens, $this->functionStart+1, ($this->functionStop - $this->functionStart - 1), true);
        if(Drupal7to8_Utility_ParseInfoHookArray::containsLogic($tokens, $phpcsFile, $this->menu_function_whitelist)) {
          $fix = $phpcsFile->addError('Routing functionality of hook_menu() has been replaced by new routing system, conditionals found, cannot change automatically: https://drupal.org/node/1800686', $stackPtr, 'HookMenuToD8');
          // Reset functionStart to 0 to stop the parser from further processing.
          $this->functionStart = $this->functionStop = 0;
          return;
        }

        // If we've gotten this far, eval the function
        $menu_array = Drupal7to8_Utility_ParseInfoHookArray::getArray(file_get_contents(__DIR__ . '/drupal_menu_bootstrap.php.inc'), $tokens, $this->functionStart, $this->functionStop);

        //print_r($menu_array);

        // We're in hook_menu, throw this fixable error (to create YML files
        $fix = $phpcsFile->addFixableError('Routing functionality of hook_menu() has been replaced by new routing system: https://drupal.org/node/1800686', $stackPtr, 'HookMenuToD8');
        if ($fix === true && $phpcsFile->fixer->enabled === true) {
          // Remove the old file.
          // @todo This is not only dangerous, it also causes an error when the file
          // it was checking suddenly vanishes. ;)
          //unlink($phpcsFile->getFilename());
        }

        for ($i = $this->functionStart; $i < $this->functionStop; $i++) {

        }
      }
  }//end process()

  protected function get_menu_item($tokens, $start, $end) {
    $menu_keys = array(
      'title', 'title callback',    );
    /*
    for($i = $start; $i < $end; $i++) {
      if(in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$stringTokens) && )
    }
    */

      return;
  }
}