/* Only run all of this code once. */
if (!defined('MENU_IS_ROOT')) {

/* Define Drupal 7 Menu Constants */
define('MENU_IS_ROOT', 0x0001);
define('MENU_VISIBLE_IN_TREE', 0x0002);
define('MENU_VISIBLE_IN_BREADCRUMB', 0x0004);
define('MENU_LINKS_TO_PARENT', 0x0008);
define('MENU_MODIFIED_BY_ADMIN', 0x0020);
define('MENU_CREATED_BY_ADMIN', 0x0040);
define('MENU_IS_LOCAL_TASK', 0x0080);
define('MENU_IS_LOCAL_ACTION', 0x0100);
define('MENU_NORMAL_ITEM', MENU_VISIBLE_IN_TREE | MENU_VISIBLE_IN_BREADCRUMB);
define('MENU_CALLBACK', 0x0000);
define('MENU_SUGGESTED_ITEM', MENU_VISIBLE_IN_BREADCRUMB | 0x0010);
define('MENU_LOCAL_TASK', MENU_IS_LOCAL_TASK | MENU_VISIBLE_IN_BREADCRUMB);
define('MENU_DEFAULT_LOCAL_TASK', MENU_IS_LOCAL_TASK | MENU_LINKS_TO_PARENT | MENU_VISIBLE_IN_BREADCRUMB);
define('MENU_LOCAL_ACTION', MENU_IS_LOCAL_TASK | MENU_IS_LOCAL_ACTION | MENU_VISIBLE_IN_BREADCRUMB);
define('MENU_CONTEXT_NONE', 0x0000);
define('MENU_CONTEXT_PAGE', 0x0001);
define('MENU_CONTEXT_INLINE', 0x0002);
define('MENU_FOUND', 1);
define('MENU_NOT_FOUND', 2);
define('MENU_ACCESS_DENIED', 3);
define('MENU_SITE_OFFLINE', 4);
define('MENU_SITE_ONLINE', 5);
define('MENU_MAX_PARTS', 9);
define('MENU_MAX_DEPTH', 9);
define('MENU_PREFERRED_LINK', '1cf698d64d1aa4b83907cf6ed55db3a7f8e92c91');

/* Wrapper function for drupal_get_path for menus */
function drupal_get_path($type, $name) {
  return '.';
}

/* Wrapper function to return t() */
function t($string, array $args = array(), array $options = array()) {
  return $string;
}

} // end !defined() check.