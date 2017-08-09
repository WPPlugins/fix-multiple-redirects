<?php
global $FIXMTPLREDIR_plugin_name;
global $FIXMTPLREDIR_plugin_version;
global $FIXMTPLREDIR_plugin_url_path;
global $wp_filter;
global $wp_actions;
global $FIXMTPLREDIR_plugin_dir;
global $FIXMTPLREDIR_isDebug;
global $FIXMTPLREDIR_basedir;
global $FIXMTPLREDIR_baseurl;
global $FIXMTPLREDIR_domain;
global $merged_filters;

// Includes
include_once ($FIXMTPLREDIR_plugin_dir . '/include/Displayable.php');
include_once ($FIXMTPLREDIR_plugin_dir . '/include/component/FilterCheckbox.php');
include_once ($FIXMTPLREDIR_plugin_dir . '/include/component/PluginPromotionOnContent.php');

$promotion = new PluginPromotionOnContent();
$instance["FIXMTPLREDIR_promote"] = get_option("FIXMTPLREDIR_promote");

// Handle form
if ($_POST['fix_multiple_redirects_save']) {
    update_option("canonical_filters_to_remove", $_POST["canonicalFilters"] != null ? implode(",", $_POST["canonicalFilters"]) : "");
    update_option("template_redirect_filters_to_remove", $_POST["redirectFilters"] != null ? implode(",", $_POST["redirectFilters"]) : "");

    if ($_POST["FIXMTPLREDIR_promote"] == "FIXMTPLREDIR_promote") {
        $new_instance["FIXMTPLREDIR_promote"] = true;
        $instance = $promotion->update($new_instance, $instance);
    } else {
        $new_instance["FIXMTPLREDIR_promote"] = false;
        $instance = $promotion->update($new_instance, $instance);
    }

    if ($_POST["isDebug"] == "isDebug") {
        update_option("isDebug", true);
        $FIXMTPLREDIR_isDebug = true;
    } else {
        update_option("isDebug", false);
        $FIXMTPLREDIR_isDebug = false;
    }

    if ($_POST["current_htaccess"] != null && $_POST["htaccess_fix"] == "nochanges") {
        if (FIXMTPLREDIR_setHtAccessFile($_POST["current_htaccess"])) {
            echo "<font color=\"red\">" . __(".htaccess file was stored", "fix-multiple-redirects") . "</font><br>";
        }
    }

    if ($_POST["htaccess_fix"] == "append_htaccess_file") {
        $htaccess = FIXMTPLREDIR_getHtAccessFile();
        $htaccess .= "
DirectoryIndex index.php index.php3 index.php4 index.php5 index.phtml index.htm index.html

<IfModule mod_rewrite.c>

RewriteEngine On
RewriteBase $FIXMTPLREDIR_basedir

# Force redirect to www
RewriteCond %{HTTP_HOST} ^$FIXMTPLREDIR_domain$ [NC]
RewriteRule ^(.*)$ $FIXMTPLREDIR_baseurl$FIXMTPLREDIR_basedir$1 [R=301,L]

# index.php to base
RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /index\.php\ HTTP/
RewriteRule ^index\.php$ $FIXMTPLREDIR_baseurl/ [R=301,L]

# is existing file or directory and remove index.php
RewriteCond %{REQUEST_FILENAME} -f
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule . - [L,NS,PT]

# Parametrized url
RewriteCond \.(php|php3|php4|php5|phtml|htm|html)\?.*$ $1
RewriteRule . - [L,NS,PT]

# is not existing file or directory, must be parsed by application
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . / [L,NS,PT]


</IfModule>";
        if (FIXMTPLREDIR_setHtAccessFile($htaccess)) {
            echo "<font color=\"red\">" . __(".htaccess file is stored", "fix-multiple-redirects") . "</font><br>";
        }
    }

    if ($_POST["htaccess_fix"] == "replace_htaccess_file") {
        if (FIXMTPLREDIR_backupHtAccessFile()) {
            echo "<font color=\"red\">" . __(".htaccess file is backed up", "fix-multiple-redirects") . "</font><br>";
        }

        $htaccess = "
DirectoryIndex index.php index.php3 index.php4 index.php5 index.phtml index.htm index.html

<IfModule mod_rewrite.c>

RewriteEngine On
RewriteBase $FIXMTPLREDIR_basedir

# Force redirect to www
RewriteCond %{HTTP_HOST} ^$FIXMTPLREDIR_domain$ [NC]
RewriteRule ^(.*)$ $FIXMTPLREDIR_baseurl$FIXMTPLREDIR_basedir$1 [R=301,L]

# index.php to base
RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /index\.php\ HTTP/
RewriteRule ^index\.php$ $FIXMTPLREDIR_baseurl/ [R=301,L]

# is existing file or directory and remove index.php
RewriteCond %{REQUEST_FILENAME} -f
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule . - [L,NS,PT]

# Parametrized url
RewriteCond \.(php|php3|php4|php5|phtml|htm|html)\?.*$ $1
RewriteRule . - [L,NS,PT]

# is not existing file or directory, must be parsed by application
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . / [L,NS,PT]


</IfModule>";
        if (FIXMTPLREDIR_setHtAccessFile($htaccess)) {
            echo "<font color=\"red\">" . __(".htaccess file is stored", "fix-multiple-redirects") . "</font>";
        }
    }
}

$canonicalFilterCheckbox = new FilterCheckbox();
$redirectFilterCheckbox = new FilterCheckbox();
?>

<div class="wrap">

    <h2><img width="32" height="32" src="<?php print $FIXMTPLREDIR_plugin_url_path . "/images/fix-icon.png"; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php print $FIXMTPLREDIR_plugin_name . " " . $FIXMTPLREDIR_plugin_version; ?></h2>
    <p>
        Lets start to configure, our fix.<br>
        <br>
        <b>What i need to know about configuration, before i start configure?</b><br>
        Configuration of fix is devided to three main parts<br>
        - disabling canonical redirect filters<br>
        - disabling redirect filters<br>
        - fix .htaccess file by manualy or generated/replacing existing one.<br>
        Purpose of this fix is to prevent cyclic redirects, you have to go carefuly throuth filters and .htaccess rules<br>
        and disable all options which are going to do redirect of cycled urls, what is our issue.<br>
        <br>
        <b>Other known issues</b><br>
        - Fix JavaScript code for Google Plus One Like button, which contained buggy code with multiple redirecting or remove component from page.<br>
        - Remove another JavaScript code which doing replace of URL over window.location object seems to be buggy for IE6, Firefox.<br>
    </p>
    <form method="post" action="">
        <input type="hidden" name="fix_multiple_redirects_save" value="fix_multiple_redirects_save">
        <h3>Debuging</h3>
        <div id="debuging" style="height:16px;padding:10px;margin:10px;width:450px; border: 1px black solid;">
            <input id="isDebug" name="isDebug" style="clear: left; float: left; margin-left: 10px;" type="checkbox" <?php echo ($FIXMTPLREDIR_isDebug) ? "checked" : ""; ?> value="isDebug">
            <label for="isDebug" style="float: left;margin-left: 10px;"><?php _e("Enable debuging", "fix-multiple-redirects"); ?></label>
        </div>
        <?php
        $canonical_filters_to_remove_selected = get_option("canonical_filters_to_remove");
        $canonical_filters_to_remove = $canonical_filters_to_remove_selected;

        $fc = $canonical_filters_to_remove != "" ? 1 : 0;

        foreach (($wp_filter["redirect_canonical"] ? array_keys($wp_filter["redirect_canonical"]) : array()) as $priority) {
            foreach ((array_keys($wp_filter["redirect_canonical"][$priority]) ? array_keys($wp_filter["redirect_canonical"][$priority]) : array()) as $f) {
                if (preg_match("(([-fa-f0-9]{32}).{1,})", $f) > 0) {
                    $func = substr($f, 32);
                    $canonical_filters_to_remove .= ($fc == 0) ? $func : ",$func";
                    $fc++;
                } else {
                    $func = $f;
                    $canonical_filters_to_remove .= ($fc == 0) ? $func : ",$func";
                    $fc++;
                }
            }
        }



// Remove duplicity
        $canonical_filters_to_remove_arr = $canonical_filters_to_remove ? explode(",", $canonical_filters_to_remove) : array();
        $canonical_filters_to_remove_selected_arr = $canonical_filters_to_remove_selected ? explode(",", $canonical_filters_to_remove_selected) : array();

        $canonical_filters_to_remove_arr = array_unique($canonical_filters_to_remove_arr);
        $canonical_filters_to_remove_selected_arr = array_unique($canonical_filters_to_remove_selected_arr);

//
        $fc = count($canonical_filters_to_remove_arr);
        ?>
        <h3>Canonical filters to disable</h3>
        <div id="canonical_filters" style="height:<?php echo (($fc > 0) ? $fc : 1) * 16; ?>px;padding:10px;margin:10px;width:450px; border: 1px black solid;">
            <?php
            if ($fc > 0) {
                $canonicalFilterCheckbox->display($canonical_filters_to_remove_arr, $canonical_filters_to_remove_selected_arr, "canonicalFilters", "canonicalFilters");
            } else {
                echo _e("No filters are registered, yet.", "fix-multiple-redirects");
            }
            ?>
        </div>
        <?php
        $template_redirect_filters_to_remove_selected = get_option("template_redirect_filters_to_remove");
        $template_redirect_filters_to_remove = $template_redirect_filters_to_remove_selected;

        $fc = $template_redirect_filters_to_remove != "" ? 1 : 0;


        foreach (($wp_filter["template_redirect"] ? array_keys($wp_filter["template_redirect"]) : array()) as $priority) {
            foreach ((array_keys($wp_filter["template_redirect"][$priority]) ? array_keys($wp_filter["template_redirect"][$priority]) : array()) as $f) {
                if (preg_match("(([-fa-f0-9]{32}).{1,})", $f) > 0) {
                    $func = substr($f, 32);
                    $template_redirect_filters_to_remove .= ($fc == 0) ? $func : ",$func";
                    $fc++;
                } else {
                    $func = $f;
                    $template_redirect_filters_to_remove .= ($fc == 0) ? $func : ",$func";
                    $fc++;
                }
            }
        }



        // Remove duplicity
        $template_redirect_filters_to_remove_arr = $template_redirect_filters_to_remove ? explode(",", $template_redirect_filters_to_remove) : array();
        $template_redirect_filters_to_remove_selected_arr = $template_redirect_filters_to_remove_selected ? explode(",", $template_redirect_filters_to_remove_selected) : array();

        $template_redirect_filters_to_remove_arr = array_unique($template_redirect_filters_to_remove_arr);
        $template_redirect_filters_to_remove_selected_arr = array_unique($template_redirect_filters_to_remove_selected_arr);

        //
        $fc = count($template_redirect_filters_to_remove_arr);
        ?>
        <h3>Template Redirect filters to disable</h3>
        <div id="redirect_filters" style="height:<?php echo (($fc > 0) ? $fc : 1) * 16; ?>px;padding:10px;margin:10px;width:450px; border: 1px black solid;">
            <?php
            if ($fc > 0) {
                $redirectFilterCheckbox->display($template_redirect_filters_to_remove_arr, $template_redirect_filters_to_remove_selected_arr, "redirectFilters", "redirectFilters");
            } else {
                echo _e("No filters are registered, yet.", "fix-multiple-redirects");
            }
            ?>
        </div>
        <?php
        $rewrite_rules_filters_to_remove_selected = get_option("rewrite_rules_filters_to_remove");
        $rewrite_rules_filters_to_remove = $rewrite_rules_filters_to_remove_selected;

        $fc = $rewrite_rules_filters_to_remove != "" ? 1 : 0;


        foreach (($wp_filter["rewrite_rules_array"] ? array_keys($wp_filter["rewrite_rules_array"]) : array()) as $priority) {
            foreach ((array_keys($wp_filter["rewrite_rules_array"][$priority]) ? array_keys($wp_filter["rewrite_rules_array"][$priority]) : array()) as $f) {
                if (preg_match("(([-fa-f0-9]{32}).{1,})", $f) > 0) {
                    $func = substr($f, 32);
                    $rewrite_rules_filters_to_remove .= ($fc == 0) ? $func : ",$func";
                    $fc++;
                } else {
                    $func = $f;
                    $rewrite_rules_filters_to_remove .= ($fc == 0) ? $func : ",$func";
                    $fc++;
                }
            }
        }



        // Remove duplicity
        $rewrite_rules_filters_to_remove_arr = $rewrite_rules_filters_to_remove ? explode(",", $rewrite_rules_filters_to_remove) : array();
        $rewrite_rules_filters_to_remove_selected_arr = $rewrite_rules_filters_to_remove_selected ? explode(",", $rewrite_rules_filters_to_remove_selected) : array();

        $rewrite_rules_filters_to_remove_arr = array_unique($rewrite_rules_filters_to_remove_arr);
        $rewrite_rules_filters_to_remove_selected_arr = array_unique($rewrite_rules_filters_to_remove_selected_arr);

        //
        $fc = count($rewrite_rules_filters_to_remove_arr);
        ?>
        <h3>Rewrite Rules filters to disable</h3>
        <div id="redirect_filters" style="height:<?php echo (($fc > 0) ? $fc : 1) * 16; ?>px;padding:10px;margin:10px;width:450px; border: 1px black solid;">
            <?php
            if ($fc > 0) {
                $redirectFilterCheckbox->display($rewrite_rules_filters_to_remove_arr, $rewrite_rules_filters_to_remove_selected_arr, "rewriteFilters", "rewriteFilters");
            } else {
                echo _e("No filters are registered, yet.", "fix-multiple-redirects");
            }
            ?>
        </div>
        <?php
        ?>
        <h3>.htaccess file Fix</h3>
        <p>For generated part of .htaccess file i was inspired by <a href="http://www.webmasterworld.com/apache/4053973.htm">Jim Morgan's wordpress htaccess</a> with few modifications. (will speed up your mod_rewrite rules more than two times)</p>
        <div id="htaccess_options" style="height: 48px;padding:10px;margin:10px;width:450px; border: 1px black solid;">
            <input style="clear: left; float: left; margin-left: 10px;" id="htaccess_fix_nochanges" type="radio" name="htaccess_fix" value="nochanges" checked><label for="htaccess_fix_nochanges" style="float: left;margin-left: 10px;"><?php _e("No changes", "fix-multiple-recirects"); ?></label>
            <input style="clear: left; float: left; margin-left: 10px;" id="htaccess_fix_append_htaccess_file" type="radio" name="htaccess_fix" value="append_htaccess_file"><label for="htaccess_fix_append_htaccess_file" style="float: left;margin-left: 10px;"><?php _e("Will append changes to file", "fix-multiple-recirects"); ?></label>
            <input style="clear: left; float: left; margin-left: 10px;" id="htaccess_fix_replace_htaccess_file" type="radio" name="htaccess_fix" value="replace_htaccess_file"><label for="htaccess_fix_replace_htaccess_file" style="float: left;margin-left: 10px;"><?php _e("!!! Will replace file, with new configuration, file will be backed up !!!", "fix-multiple-recirects"); ?></label>
        </div>
        <h3>Edit current .htaccess file</h3>
        <div id="htaccess_file">
            <textarea name="current_htaccess" cols="120" rows="25"><?php echo FIXMTPLREDIR_getHtAccessFile(); ?></textarea>
        </div>

        <h3>Promotion and Donation</h3>
        <div id="promotion_donation">
            <p>
                You can promote us or donate only <b>1$</b>.
            </p>
            <p>
                <?php
                $args["exceptedValue"] = "FIXMTPLREDIR_promote";
                $args["name"] = "FIXMTPLREDIR_promote";
                $args["id"] = "FIXMTPLREDIR_promote";
                $promotion->form($args, $instance);
                ?>
            </p>

            <p>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="NSHFBHQ9XM9NJ">
                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>
            </p>
            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>

    </form>
</div>
</div>