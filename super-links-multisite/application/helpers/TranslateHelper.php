<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class TranslateHelper {

    public static function printTranslate($text = ''){
        echo self::getTranslate($text);
    }

    public static function getTranslate($text = ''){
        return __($text, SUPER_LINKS_PLUGIN_NAME);
    }
}