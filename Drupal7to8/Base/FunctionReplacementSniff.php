<?php
/**
 * Drupal7to8_Base_FunctionReplacementSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Drupal7to8_Base_FunctionReplacementSniff.
 *
 * Extends the capabilities of Generic_Sniffs_PHP_ForbiddenFunctionsSniff with
 * two things:
 * 1) fixability
 * 2) optionally: dynamic argument replacement
 * 3) optionally: insertion of use statements
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Base_FunctionReplacementSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff {

  protected $forbiddenFunctions = array();

  protected $dynamicArgumentReplacements = array();

  protected $useStatements = array();

  protected $message = '';

  protected $code = '';

  /**
   * {@inheritdoc}
   */
  protected function addError($phpcsFile, $stackPtr, $function, $pattern = NULL) {
    $fix = FALSE;
    $message = strtr($this->message, array('!function' => $function));

    if ($this->hasFix($function, $pattern)) {
      $fix = $phpcsFile->addFixableError($message, $stackPtr, $this->code);
    }
    elseif ($phpcsFile->fixer->enabled === TRUE) {
      $this->insertFixMeComment($phpcsFile, $stackPtr, $message);
    }

    if ($fix === TRUE && $phpcsFile->fixer->enabled === TRUE) {
      $tokens = $phpcsFile->getTokens();

      if (isset($this->dynamicArgumentReplacements[$function])) {
        $replacement_info = $this->dynamicArgumentReplacements[$function];
        $arguments = $replacement_info['arguments'];

        // Find the arguments that need to be moved around, remove them, and
        // dynamically build the replacement string for this function call.
        $replacement = $replacement_info['string'];
        foreach ($arguments as $argument) {
          // Find the token range representing the nth argument.
          $result = $this->findNthArgument($phpcsFile, $stackPtr, $argument);
          // Return early if there is no nth argument.
          if ($result === FALSE) {
            continue;
          }
          list($arg_start, $arg_end, $remove_start, $remove_end) = $result;

          // Get the string representation of the token range.
          $content = Drupal7to8_Utility_TokenRange::getContent($tokens, $arg_start, $arg_end);
          $replacement = strtr($replacement, array('$' . $argument => $content));
          // Remove the nth argument from the original function call.
          Drupal7to8_Utility_TokenRange::remove($phpcsFile->fixer, $remove_start, $remove_end);
        }

        // Update the function call.
        $phpcsFile->fixer->replaceToken($stackPtr, $replacement);
      }
      else {
        $phpcsFile->fixer->replaceToken($stackPtr, $this->forbiddenFunctions[$function]);
      }

      $this->insertUseStatement($phpcsFile, $function, $pattern);
    }
    elseif ($fix === FALSE) {
      $phpcsFile->addError($message, $stackPtr, $this->code);
    }
  }

  /**
   * Checks whether this sniff has a replacement for the given function/pattern.
   *
   * @param string $function
   * @param string|null $pattern
   * @return boolean
   */
  protected function hasFix($function, $pattern = NULL) {
    if ($pattern === NULL) {
      $pattern = $function;
    }

    return $this->forbiddenFunctions[$pattern] !== NULL;
  }

  /**
   * Insert a use statement, if the given function needs it.
   */
  protected function insertUseStatement(PHP_CodeSniffer_File $phpcsFile, $function, $pattern) {
    if ($pattern === NULL) {
      $pattern = $function;
    }

    if (isset($this->useStatements[$pattern])) {
      $class = $this->useStatements[$pattern];
      $phpcsFile->fixer->addContent(0, "\nuse $class;");
    }
  }

  /**
   * Inserts a "@fixme" comment before a function call.
   *
   * Also prefixes the function name with fixme_ to prevent an endless loop.
   */
  protected function insertFixMeComment(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $message) {
    $phpcsFile->fixer->addContentBefore($stackPtr, 'fixme_');

    // Prefix the "@fixme" comment with as much whitespace as exists before the
    // function declaration.
    $tokens = $phpcsFile->getTokens();
    $whitespace = $tokens[$stackPtr - 1]['content'];

    $phpcsFile->fixer->addContentBefore($stackPtr - 1, "\n" . $whitespace . '/** @fixme '. $message . " */\n");
  }

  /**
   * Given a stack pointer that points to a function invocation or definition,
   * retrieve the nth argument.
   *
   * @param PHP_CodeSniffer_File $phpcsFile
   * @param int $stackPtr
   * @param int $n
   *   The nth function argument. Zero-indexed.
   *
   * @return
   *   FALSE if there is no nth argument, otherwise an array with 4 values:
   *   - the token where the nth argument starts
   *   - the token where the nth argument ends
   *   - the token where removal of the nth argument should begin
   *   - the token where removal of the nth argument should end
   */
  protected function findNthArgument(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $n) {
    $tokens = $phpcsFile->getTokens();

    // Find If the next non-whitespace token after the function or method call
    // is not an opening parenthesis then it cant really be a *call*.
    $openParenthesis = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), NULL, TRUE);
    $closeParenthesis = $tokens[$openParenthesis]['parenthesis_closer'];

    $arg_start = $openParenthesis + 1;
    $arg_end = $closeParenthesis - 1;
    $remove_start = $openParenthesis + 1;
    $remove_end = $closeParenthesis - 1;

    // Keep increasing the starting point until we've reached the nth argument.
    if ($n > 0) {
      $comma = $arg_start;

      for ($arg = 1; $arg <= $n; $arg++) {
        $comma = $phpcsFile->findNext(T_COMMA, $comma + 1, $closeParenthesis);
        if ($comma === FALSE) {
          return FALSE;
        }
        elseif ($arg === $n) {
          $arg_start = $comma + 1;
          $remove_start = $comma;
        }
      }
    }
    // But ignore leading whitespace.
    $arg_start = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $arg_start, NULL, TRUE);

    // If there still is a next comma, and hence a next argument, then we should
    // stop before that comma instead of before the closing parenthesis.
    $nextComma = $phpcsFile->findNext(T_COMMA, ($arg_start + 1), $closeParenthesis);
    if ($nextComma !== FALSE) {
      $arg_end = $remove_end = $nextComma - 1;
    }

    return array($arg_start, $arg_end, $remove_start, $remove_end);
  }

}
