<?php
/**
 * Drupal7to8_Sniffs_InfoFiles_YamlVerifySniff.
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

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Verifies that YAML syntax in .info.yml is correct.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_InfoFiles_YamlVerifySniff implements PHP_CodeSniffer_Sniff {

  /**
   * {@inheritdoc}
   */
  public function register() {
    // Fire on text outside of PHP.
    return array(T_INLINE_HTML);
  }

  /**
   * {@inheritdoc}
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
    // Only process on .info.yml files.
    $fileExtension = strtolower(substr($phpcsFile->getFilename(), -8));
    if ($fileExtension !== 'info.yml') {
      return;
    }

    // Only run once per file.
    $tokens = $phpcsFile->getTokens();
    if ($tokens[$stackPtr]['line'] !== 1) {
      return;
    }

    // Figure out if the .info file is simply renamed, or already in YAML.
    $info = array();
    try {
      $info = Yaml::parse($phpcsFile->getFilename());
    }
    catch (ParseException $e) {
      $fix = $phpcsFile->addFixableError('.info.yml file did not parse as valid YAML: https://drupal.org/node/1935708', $stackPtr, 'YamlVerify');
      if ($fix === true && $phpcsFile->fixer->enabled === true) {
        $file = file_get_contents($phpcsFile->getFilename());
        $info = $info = $this->drupalParseInfoFormat($file);
        if (!empty($info)) {
          // Write out info as YAML instead.
          $info = Yaml::dump($info);
          file_put_contents($phpcsFile->getFilename(), $info);
        }
      }
    }

    // Now we should have valid YAML. Check for required/extraneous properties.

    // type: module
    if (!array_key_exists('type', $info)) {
      $fix = $phpcsFile->addFixableError('Missing required "type" property: https://drupal.org/node/1935708', $stackPtr, 'YamlVerify');
      if ($fix === true && $phpcsFile->fixer->enabled === true) {
        // Add it.
        // @todo: If we start fixing themes and profiles, we can't just do this.
        $info['type'] = 'module';
      }
    }

    // core: 8.x
    if ($info['core'] == '7.x') {
      $fix = $phpcsFile->addFixableError('The "core" property must change to "8.x": https://drupal.org/node/1935708', $stackPtr, 'YamlVerify');
      if ($fix === true && $phpcsFile->fixer->enabled === true) {
        // Fix it.
        $info['core'] = '8.x';
      }
    }

    // files array
    if (array_key_exists('files', $info)) {
      // Don't think we should can/should fix this one. Raise an error instead.
      $phpcsFile->addError('Drupal 8 now uses PSR class loading; remove "files" entries from .info.yml file.: https://drupal.org/node/1320394', $stackPtr, 'YamlVerify');
    }

    // @todo: Dependencies that are no longer needed in 8.x.

    // All done with our checks; write the YAML out again.
    if ($phpcsFile->fixer->enabled === true) {
      file_put_contents($phpcsFile->getFilename(), $info);
    }
  }

  /**
   * Parses a Drupal info file. Copied from Drupal core drupal_parse_info_format().
   *
   * @param string $data The contents of the info file to parse
   *
     * @return array The info array.
   */
  public static function drupalParseInfoFormat($data)
  {
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