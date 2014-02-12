<?php
/**
 * Drupal7to8_Sniffs_Aggregator_FeedItemsLoadSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * aggregator_feed_items_load() renamed to aggregator_load_feed_items().
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal7to8_Sniffs_Aggregator_FeedItemsLoadSniff extends Drupal7to8_Sniffs_Functions_FunctionReplacementSniff {

  protected $message = 'aggregator_feed_items_load() renamed to aggregator_load_feed_items(): https://drupal.org/node/1295398';

  protected $code = 'FeedItemsLoad';

  protected $forbiddenFunctions = array(
    'aggregator_feed_items_load' => 'aggregator_load_feed_items',
  );

}
