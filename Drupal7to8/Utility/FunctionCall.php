<?php

class Drupal7to8_Utility_FunctionCall {

  /**
   * Determine the module name based on the file being examined.
   *
   * @param PHP_CodeSniffer_File $phpcsFile
   *   The code sniffer file.
   * @return string|null
   *   The module name if it can be determined, NULL if it cannot.
   */
  static public function isFunctionCall(PHP_CodeSniffer_File $phpcsFile, $tokens, $stackPtr) {
    if($tokens[$stackPtr]['type'] !== "T_STRING") {
      return FALSE;
    }
    $ignore = array(
               T_DOUBLE_COLON,
               T_OBJECT_OPERATOR,
               T_FUNCTION,
               T_CONST,
               T_PUBLIC,
               T_PRIVATE,
               T_PROTECTED,
               T_AS,
               T_NEW,
               T_INSTEADOF,
               T_NS_SEPARATOR,
               T_IMPLEMENTS,
              );

    $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
    if (in_array($tokens[$prevToken]['code'], $ignore) === true) {
        // Not a call to a PHP function.
      return FALSE;
    }

    $nextToken = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($stackPtr + 1));
    $backptr = ($tokens[$nextToken-1]['type'] == T_WHITESPACE) ? 2 : 1;
    if ($nextToken - $backptr == $stackPtr) {
      return true;
    }

    return false;
  }
}

