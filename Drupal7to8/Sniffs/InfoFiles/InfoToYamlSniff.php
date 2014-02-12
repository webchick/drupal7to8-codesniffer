<?php
/**
 * Drupal7to8_Sniffs_InfoFiles_InfoToYamlSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Dumper;

/**
 * Warns that .info files are now .info.yml files, and attempts to rename them.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_InfoFiles_InfoToYamlSniff implements PHP_CodeSniffer_Sniff {

  /**
   * {@inheritdoc}
   */
  public function register() {
    if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
      require_once __DIR__ . '/../../vendor/autoload.php';
    }
    else {
      print('HEY! You have to install Composer in order to get Symfony in order to parse YAML because yeah. https://getcomposer.org/download/');
      return array();
    }

    // Fire on text outside of PHP.
    return array(T_INLINE_HTML);
  }

  /**
   * {@inheritdoc}
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    // Only process the rename on .info files.
    $needRename = FALSE;
    $fileExtension = strtolower(substr($phpcsFile->getFilename(), -4));
    if ($fileExtension == 'info') {
      $needRename = TRUE;

      // If .info.yml file already exists, our work here is done. The parsing
      // will run on the info.yml file as well separately.
      if (file_exists($phpcsFile->getFilename() . '.yml')) {
        return;
      }
    }
    else {
      // Only process YAML fixes on .info.yml files.
      $fileExtension = strtolower(substr($phpcsFile->getFilename(), -8));
      if ($fileExtension !== 'info.yml') {
        // Not an info or info.yml file.
        return;
      }
    }

    // Only run once per file.
    $tokens = $phpcsFile->getTokens();
    if ($tokens[$stackPtr]['line'] !== 1) {
      return;
    }

    // Figure out if we are dealing with an .info or YAML format.
    $info = array();
    try {
      $info = Yaml::parse($phpcsFile->getFilename());
    }
    catch (ParseException $e) {
      if (!$needRename) {
        $fix = $phpcsFile->addFixableError('.info.yml file did not parse as valid YAML: https://drupal.org/node/1935708', $stackPtr, 'YamlVerify');
      }
      else {
        $fix = $phpcsFile->addFixableError('.info files are now .info.yml files: https://drupal.org/node/1935708', $stackPtr, 'InfoToYaml');
      }
# @todo For some reason, $phpcsFile->fixer->enabled is not true here when running phpcbf. wtf.
#     if ($fix === true && $phpcsFile->fixer->enabled === true) {
        $file = file_get_contents($phpcsFile->getFilename());
        $info = $this->drupalParseInfoFormat($file);
#     }
    }

    // Now we should have valid YAML. Check for required/extraneous properties.

    // type: module
    if (!array_key_exists('type', $info)) {
      $fix = $phpcsFile->addFixableError('Missing required "type" property: https://drupal.org/node/1935708', $stackPtr, 'YamlVerify');
# @todo For some reason, $phpcsFile->fixer->enabled is not true here when running phpcbf. wtf.
#     if ($fix === true && $phpcsFile->fixer->enabled === true) {
        // Add it.
        // @todo: If we start fixing themes and profiles, we can't just do this.
        $info['type'] = 'module';
#    }
    }

    // core: 8.x
    if ($info['core'] == '7.x') {
      $fix = $phpcsFile->addFixableError('The "core" property must change to "8.x": https://drupal.org/node/1935708', $stackPtr, 'YamlVerify');
# @todo For some reason, $phpcsFile->fixer->enabled is not true here when running phpcbf. wtf.
#     if ($fix === true && $phpcsFile->fixer->enabled === true) {
        // Fix it.
        $info['core'] = '8.x';
#      }
     }

    // files array
    if (array_key_exists('files', $info)) {
      // We can't really fix this, so leave the warning here.
      $phpcsFile->addError('Drupal 8 now uses PSR class loading; remove "files" entries from .info.yml file.: https://drupal.org/node/1320394', $stackPtr, 'YamlVerify');
    }

    // styles and scripts array
    if (array_key_exists('stylesheets', $info) || array_key_exists('scripts', $info)) {
      // Don't think we can fix this one.
      $phpcsFile->addError('Modules can no longer add stylesheets/scripts via their .info.yml file: https://drupal.org/node/1876152', $stackPtr, 'YamlVerify');
    }

    // @todo: Dependencies that are no longer needed in 8.x.

    // All done with our checks; write the YAML out again.
# @todo For some reason, $phpcsFile->fixer->enabled is not true here when running phpcbf. wtf.
#   if ($phpcsFile->fixer->enabled === true) {
      $yaml = Yaml::dump($info);
      file_put_contents($phpcsFile->getFilename() . ($needRename ? '.yml' : ''), $yaml);

      // @todo Leave a @todo in the original .info file to remove it.
      //$contents = "; @todo: Remove this file once your module is ported.\n" . $contents;
#   }
  }

  /**
   * Parses a Drupal info file. Copied from Drupal core drupal_parse_info_format().
   *
   * @param string $data The contents of the info file to parse
   *
   * @return array The info array.
   */
  public static function drupalParseInfoFormat($data) {
    $info = array();
    $constants = get_defined_constants();

    if (preg_match_all('
      @^\s*                           # Start at the beginning of a line, ignoring leading whitespace
      ((?:
        [^=;\[\]]|                    # Key names cannot contain equal signs, semi-colons or square brackets,
        \[[^\[\]]*\]                  # unless they are balanced and not nested
      )+?)
      \s*=\s*                         # Key/value pairs are separated by equal signs (ignoring white-space)
      (?:
        ("(?:[^"]|(?<=\\\\)")*")|     # Double-quoted string, which may contain slash-escaped quotes/slashes
        (\'(?:[^\']|(?<=\\\\)\')*\')| # Single-quoted string, which may contain slash-escaped quotes/slashes
        ([^\r\n]*?)                   # Non-quoted string
      )\s*$                           # Stop at the next end of a line, ignoring trailing whitespace
      @msx', $data, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
          // Fetch the key and value string.
          $i = 0;
          foreach (array('key', 'value1', 'value2', 'value3') as $var) {
            $$var = isset($match[++$i]) ? $match[$i] : '';
          }
          $value = stripslashes(substr($value1, 1, -1)) . stripslashes(substr($value2, 1, -1)) . $value3;

          // Parse array syntax.
          $keys = preg_split('/\]?\[/', rtrim($key, ']'));
          $last = array_pop($keys);
          $parent = &$info;

          // Create nested arrays.
          foreach ($keys as $key) {
            if ($key == '') {
              $key = count($parent);
            }
            if (!isset($parent[$key]) || !is_array($parent[$key])) {
              $parent[$key] = array();
            }
            $parent = &$parent[$key];
          }

          // Handle PHP constants.
          if (isset($constants[$value])) {
            $value = $constants[$value];
          }

          // Insert actual value.
          if ($last == '') {
            $last = count($parent);
          }
          $parent[$last] = $value;
        }
    }
    return $info;

  }//end drupalParseInfoFormat()

}
