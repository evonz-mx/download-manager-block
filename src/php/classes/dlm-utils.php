<?php 

namespace DLM;


/**
 * 
 */
Class DLMBlock_Utils {

    /**
     * removes from the front of string
     */
    public static function remove_prefix($prefix, $str) {
        if (substr($str, 0, strlen($prefix)) == $prefix) {
            $str = substr($str, strlen($prefix));
        }
        return $str;
    }

    /**
     * sorts an array of objects by "title" property
     */
    public static function sort_by_title($a, $b) {
        return strcmp($a->title, $b->title);
    }

    /**
     * slugifies a string (caches to prevent duplicates)
     */
    public static function slugify($text, $context = 'global', string $divider = '-') {

        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, $divider);
        $text = preg_replace('~-+~', $divider, $text);
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return DLMBlock_Utils::uniquify($text, $text, $context, $divider, 1);
    }

    /**
     * ensures that slugs are unique
     */
    public static function uniquify($text, $current_text, $context = 'global', $divider, $i) {
        global $dlm_slug_cache;

        if (!array_key_exists($context, $dlm_slug_cache)) {
            $dlm_slug_cache[$context] = [];
        }

        if (in_array($current_text, $dlm_slug_cache[$context])) {
            $current_text = $text . $divider . $i;
            $i++;
            return DLMBlock_Utils::uniquify($text, $current_text, $context, $divider, $i);
        }

        $dlm_slug_cache[$context][] = $current_text;

        return $current_text;
    }

    public static function filter_only_plugin($file) {
        return $file->type === 'plugin';
    }
    public static function filter_only_tool($file) {
        return $file->type === 'tool';
    }
    public static function filter_only_platform($file) {
        return $file->type === 'platform';
    }
}