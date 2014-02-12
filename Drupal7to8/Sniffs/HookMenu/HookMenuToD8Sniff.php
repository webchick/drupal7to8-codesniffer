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

  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register()
  {
      return array(T_FUNCTION,
        T_RETURN,
      );

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

      if ($tokens[$stackPtr]['type'] == 'T_FUNCTION' && $tokens[$stackPtr+2]['content'] == $filename_info['filename'] . '_menu') {

        $this->functionStart  = $tokens[$stackPtr]['scope_opener'];
        $this->functionStop = $tokens[$stackPtr]['scope_closer'];

        for($i = $this->functionStart; $i < $this->functionStop; $i++) {
          if(in_array($tokens[$stackPtr], PHP_CodeSniffer_Tokens::$scopeOpeners)) {
            $fix = $phpcsFile->addError('Routing functionality of hook_menu() has been replaced by new routing system, conditionals found, cannot change automatically: https://drupal.org/node/1800686', $stackPtr, 'HookMenuToD8');

            // Reset functionStart to 0 to stop the parser from further processing.
            $this->functionStart = 0;
            return;
          }
        }

        // We're in hook_menu, throw this fixable error (to create YML files
        $fix = $phpcsFile->addFixableError('Routing functionality of hook_menu() has been replaced by new routing system: https://drupal.org/node/1800686', $stackPtr, 'HookMenuToD8');
        if ($fix === true && $phpcsFile->fixer->enabled === true) {
          // Remove the old file.
          // @todo This is not only dangerous, it also causes an error when the file
          // it was checking suddenly vanishes. ;)

          //unlink($phpcsFile->getFilename());
        }
      }

      // We found an array, get me a value
      // We need to define an array parent, and the keys for those parents should
      // have another array (double array or open paren after it.
      if ($this->functionStart != 0 && ($tokens[$stackPtr]['type'] == 'T_RETURN' &&
          $stackPtr > $this->functionStart && $stackPtr < $this->functionStop)) {
        if ($tokens[$stackPtr+2]['type'] == 'T_VARIABLE') {
          // Get the token that contains the Variable. Parent hasn't be set yet
          for ($i = $this->functionStart; $i < $this->functionStop; $i++) {
            // Compare the variable we're looking at with the variable in the return.
            if ($tokens[$i]['type'] == 'T_VARIABLE' && $tokens[$i]['content'] == $tokens[$stackPtr+2]['content']) {
              // Get all of the string tokens
              $this->return_var = $tokens[$i];
              continue;
            }
          }
          print_r($this->return_var);
          for ($i = $this->functionStart; $i < $this->functionStop; $i++) {
            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$stringTokens) &&
              $tokens[$i-1]['type'] == "T_OPEN_SQUARE_BRACKET" &&
              $tokens[$i+1]['type'] == "T_CLOSE_SQUARE_BRACKET" &&
              $tokens[$i+3]['type'] == "T_EQUAL" &&
              $tokens[$i+5]['type'] == "T_ARRAY") {
              $this->menu[$tokens[$i]['content']] = $this->get_menu_item($tokens, $tokens[$i+5]['parenthesis_opener'], $tokens[$i+5]['parenthesis_closer']);
            }
          }
        }
        print_r($this->menu);
      }

  }//end process()

  protected function get_menu_item($tokens, $start, $end) {
    $menu_keys = array(
      'title', 'title callback',

    );
    /*
    for($i = $start; $i < $end; $i++) {
      if(in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$stringTokens) && )
    }
    */

      return "hello";
  }
}