<?php

/*
  Plugin Name: Fix Multiple Redirects
  Plugin URI: http://www.devtech.cz/
  Description: Fix multiple redirects and canonical redirects. And increase speed of wordpress fine url recognition for SEO by .htaccess file.
  Version: 1.2.3
  License: GNU v3 \
  This program comes with ABSOLUTELY NO WARRANTY. \
  This is free software, and you are welcome to redistribute it \
  under certain conditions; type `show c' for details. \
  Author: Copyright (C) <2012> Juraj Puchký
  Author URI: http://www.devtech.cz/

 */


if (!isset($FIXMTPLREDIR_locale))
    $FIXMTPLREDIR_locale = '';

// Pre-2.6 compatibility
if (!defined('WP_CONTENT_URL'))
    define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if (!defined('WP_PLUGIN_URL'))
    define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');

$FIXMTPLREDIR_plugin_basename = plugin_basename(dirname(__FILE__));

if (basename(dirname(__FILE__)) == "mu-plugins") {
    $FIXMTPLREDIR_plugin_url_path = WPMU_PLUGIN_URL . '/fix-multiple-redirects';
    $FIXMTPLREDIR_plugin_dir = WPMU_PLUGIN_DIR . '/fix-multiple-redirects';
} else {
    $FIXMTPLREDIR_plugin_url_path = WP_PLUGIN_URL . '/' . $FIXMTPLREDIR_plugin_basename;
    $FIXMTPLREDIR_plugin_dir = WP_PLUGIN_DIR . '/' . $FIXMTPLREDIR_plugin_basename;
}


load_plugin_textdomain('fix-multiple-redirects', false, $FIXMTPLREDIR_plugin_basename . '/languages');

// Global variables
global $FIXMTPLREDIR_plugin_name, $FIXMTPLREDIR_plugin_version, $FIXMTPLREDIR_basedir, $FIXMTPLREDIR_domain;

$FIXMTPLREDIR_plugin_name = "Fix Multiple Redirects";
$FIXMTPLREDIR_plugin_version = "1.2";
$FIXMTPLREDIR_basedir = FIXMTPLREDIR_getBase();
$FIXMTPLREDIR_baseurl = get_bloginfo('wpurl');
$FIXMTPLREDIR_domain = FIXMTPLREDIR_domain();

// Fix SSL
if (is_ssl())
    $FIXMTPLREDIR_plugin_url_path = str_replace('http:', 'https:', $FIXMTPLREDIR_plugin_url_path);

function FIXMTPLREDIR_domain() {
    global $FIXMTPLREDIR_baseurl;
    $domain = str_replace('http://', '', $FIXMTPLREDIR_baseurl);
    $domain = str_replace('https://', '', $domain);
    $a = explode("/", $domain);
    $domain = $a[0];
    $parts = explode(".", $domain);
    if (count($parts) > 2) {
        $domain = $parts[count($parts) - 2] . "." . $parts[count($parts) - 1];
    } else {
        $domain = implode(".", $parts);
    }
    return $domain;
}

function FIXMTPLREDIR_getBase() {
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    $wpContentDir = WP_CONTENT_DIR;

    $baseDir = substr($wpContentDir, strlen($documentRoot), strlen($wpContentDir) - (strlen(basename($wpContentDir)) + strlen(
                    $documentRoot)));

    return $baseDir;
}

function FIXMTPLREDIR_register_settings() {
    register_setting('FIXMTPLREDIR-settings-group', 'canonical_filters_to_remove');
    register_setting('FIXMTPLREDIR-settings-group', 'template_redirect_filters_to_remove');
    register_setting('FIXMTPLREDIR-settings-group', 'FIXMTPLREDIR_promote');
    register_setting(
            'FIXMTPLREDIR-settings-group', 'isDebug');
}

function FIXMTPLREDIR_init_menu() {
    global $FIXMTPLREDIR_plugin_dir;
    add_options_page('Fix multiple and canonical redirects', 'Fix Multiple Redirects', 'edit_pages', basename(__FILE__), 'FIXMTPLREDIR_admin_settings_page');
}

function FIXMTPLREDIR_admin_settings_page() {
    global $FIXMTPLREDIR_plugin_dir;
    require_once($FIXMTPLREDIR_plugin_dir
            . '/include/admin-ui/settings.php');
}

function FIXMTPLREDIR_getHtAccessFile() {
    return file_get_contents(
            $_SERVER['DOCUMENT_ROOT'] . "/.htaccess");
}

function FIXMTPLREDIR_setHtAccessFile($content) {
    $content = str_replace("\\\\", "\\", $content);
    return file_put_contents($_SERVER['DOCUMENT_ROOT'
            ] . "/.htaccess", $content, LOCK_EX);
}

function FIXMTPLREDIR_backupHtAccessFile() {
    return file_put_contents($_SERVER ['DOCUMENT_ROOT'] . "/.htaccess_" .
            time(), FIXMTPLREDIR_getHtAccessFile());
}

function FIXMTPLREDIR_do_fix() {
    global $wp_filter;
    global $FIXMTPLREDIR_isDebug;
    $canonical_filters_to_remove = get_option('canonical_filters_to_remove');
    $template_redirect_filters_to_remove = get_option('template_redirect_filters_to_remove');
//TODO: condition for null to array as settings

    if ($canonical_filters_to_remove != "") {
        foreach (explode(",", $canonical_filters_to_remove) as $fpostfix) {
            foreach (array_keys($wp_filter["redirect_canonical"]) as $priority) {
                foreach (array_keys($wp_filter["redirect_canonical"][$priority]) as $f) {
                    if (preg_match("(([-fa-f0-9]{32})$fpostfix)", $f)) {
                        unset($wp_filter["redirect_canonical"][$priority][$f]);
                        if (
                                $FIXMTPLREDIR_isDebug)
                            error_log($fpostfix . " removed $f");
                    } else if ($fpostfix == $f) {
                        unset($wp_filter["redirect_canonical"][$priority][$fpostfix]);
                        if (
                                $FIXMTPLREDIR_isDebug)
                            error_log($fpostfix . " removed $f");
                    }
                }
            }
        }
    }

    if ($template_redirect_filters_to_remove != "") {
        foreach (explode(",", $template_redirect_filters_to_remove) as $fpostfix) {
            foreach (array_keys($wp_filter["template_redirect"]) as $priority) {
                foreach (array_keys($wp_filter["template_redirect"][$priority]) as $f) {
                    if (preg_match("(([-fa-f0-9]{32})$fpostfix)", $f)) {
                        unset($wp_filter["template_redirect"][$priority][$f]);
                        if ($FIXMTPLREDIR_isDebug)
                            error_log($fpostfix . " removed $f");
                    } else if ($fpostfix == $f) {
                        unset($wp_filter["template_redirect"][$priority][$fpostfix]);
                        if ($FIXMTPLREDIR_isDebug)
                            error_log($fpostfix . " removed $f");
                    }
                }
            }
        }
    }
}

function FIXMTPLREDIR_promotionOnContent($content) {
    $promote = new PluginPromotionOnContent();
    return $content . $promote->display
                    (get_option("FIXMTPLREDIR_promote"));
}

function FIXMTPLREDIR_init() {
    global $FIXMTPLREDIR_isDebug;
    global $FIXMTPLREDIR_plugin_dir;

    include_once ($FIXMTPLREDIR_plugin_dir . '/include/Displayable.php');
    include_once ($FIXMTPLREDIR_plugin_dir . '/include/component/PluginPromotionOnContent.php');

// Get global options
    $FIXMTPLREDIR_isDebug = get_option('isDebug');

    if ($FIXMTPLREDIR_isDebug == null) {
// Default enabled
        update_option("isDebug", true);
        $FIXMTPLREDIR_isDebug = true;
    }
//call register settings function
    add_action('admin_init', 'FIXMTPLREDIR_register_settings');

// Setup filters by settings
    FIXMTPLREDIR_do_fix();

// Promotion filter
    add_filter("the_content", "FIXMTPLREDIR_promotionOnContent");
// Create plugin settings menu
    add_action('admin_menu', 'FIXMTPLREDIR_init_menu');
}

add_action('init', 'FIXMTPLREDIR_init', 0);
?>