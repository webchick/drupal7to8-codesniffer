<?php
/**
 * Drupal7to8_Base_HookImplementationChangeSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Base class to build hook implementation change sniffs.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Base_HookImplementationChangeSniff implements PHP_CodeSniffer_Sniff {

  protected $hook = NULL;

  protected $is_fixable = FALSE;

  protected $renamed_hook = NULL;

  protected $has_alter_hook = FALSE;

  protected $message = '';

  protected $code = '';

  /**
   * {@inheritdoc}
   */
  public function register() {
    return array(T_FUNCTION);
  }

  /**
   * {@inheritdoc}
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
    $tokens = $phpcsFile->getTokens();

    if ($tokens[$stackPtr]['type'] === 'T_FUNCTION') {
      $function_name = $tokens[$stackPtr + 2]['content'];
      $module_name = Drupal7to8_Utility_ModuleProperties::getModuleName($phpcsFile);

      // Determine the expected function name.
      $expected = $module_name . '_' . $this->hook;
      if ($this->has_alter_hook && preg_match('/_alter$/', $function_name)) {
        $expected .= '_alter';
      }

      $matches = array();
      if ($function_name === $expected) {
        $message = strtr($this->message, array('!function' => $function_name));
        if ($this->is_fixable) {
          $fix = $phpcsFile->addFixableError($message, $stackPtr, $this->code);
          if ($fix === TRUE && $phpcsFile->fixer->enabled === TRUE) {
            $this->fix($phpcsFile, $stackPtr, $module_name, $expected);
          }
        }
        else {
          $phpcsFile->addError($message, $stackPtr, $this->code);
        }
      }
    }
  }

  /**
   * Fixes the hook implementation.
   *
   * The default implementation merely renames the hook, subclasses may want to
   * override this for more advanced hook implementation changes.
   *
   * @param PHP_CodeSniffer_File $phpcsFile
   * @param int $stackPtr
   *   Pointer to the corresponding  T_FUNCTION token.
   * @param string $module
   *   The name of the module implementing the hook.
   * @param string $hook
   *   The hook name.
   */
  protected function fix(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $module, $hook) {
    if (preg_match('/_alter$/', $hook)) {
      $new_hook = $this->renamed_hook ?: $this->renamed_hook . '_alter';
    }
    else {
      $new_hook = $this->renamed_hook ?: $this->renamed_hook;
    }
    $phpcsFile->fixer->replaceToken($stackPtr + 2, $module . '_' . $new_hook);
  }

}
