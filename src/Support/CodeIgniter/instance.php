<?php

$cwd = getcwd();
chdir(__DIR__);

define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'testing');

define('BASEPATH', $cwd . '/system/');
define('FCPATH', $cwd . '/public/');
define('APPPATH', $cwd . '/application/');
define('VIEWPATH', $cwd . '/application/views/');

define('EXT', '.php');

define('CI_ERROR_LOG_LEVEL', E_ALL ^ E_WARNING ^ E_NOTICE ^ E_STRICT ^ E_DEPRECATED);
define('CI_ERROR_DISPLAY_LEVEL', E_ALL ^ E_WARNING ^ E_NOTICE ^ E_STRICT ^ E_DEPRECATED);

require(BASEPATH . 'core/Common.php');

$charset = strtoupper(config_item('charset'));
ini_set('default_charset', $charset);

if (extension_loaded('mbstring')) {
    define('MB_ENABLED', true);
    // mbstring.internal_encoding is deprecated starting with PHP 5.6
    // and it's usage triggers E_DEPRECATED messages.
    @ini_set('mbstring.internal_encoding', $charset);
    // This is required for mb_convert_encoding() to strip invalid characters.
    // That's utilized by CI_Utf8, but it's also done for consistency with iconv.
    mb_substitute_character('none');
} else {
    define('MB_ENABLED', false);
}

// There's an ICONV_IMPL constant, but the PHP manual says that using
// iconv's predefined constants is "strongly discouraged".
if (extension_loaded('iconv')) {
    define('ICONV_ENABLED', true);
    // iconv.internal_encoding is deprecated starting with PHP 5.6
    // and it's usage triggers E_DEPRECATED messages.
    @ini_set('iconv.internal_encoding', $charset);
} else {
    define('ICONV_ENABLED', false);
}

$GLOBALS['CFG'] = & load_class('Config', 'core');
$GLOBALS['UNI'] = & load_class('Utf8', 'core');
$GLOBALS['SEC'] = & load_class('Security', 'core');

load_class('Loader', 'core');
load_class('Router', 'core');
load_class('Input', 'core');
load_class('Lang', 'core');
load_class('Benchmark', 'core');

require(BASEPATH . 'core/Controller.php');
require_once(APPPATH . 'core/MY_Controller.php');

function &get_instance()
{
    return MY_Controller::get_instance();
}

chdir($cwd);

return new MY_Controller();
