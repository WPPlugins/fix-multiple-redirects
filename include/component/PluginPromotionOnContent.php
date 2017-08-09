<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PluginPromotion
 *
 * @author Ricina
 */
class PluginPromotionOnContent  {

    //put your code here
    public function display($object=null) {
        if($object) {
            return "\n<div style=\"text-align:right;\"><a href=\"http://www.devtech.cz/\" style=\"font-size:5px;font-style: none;color: #555588\" title=\"Multiple redirects fixed by - Devtech,preshashop plugin,wordpress plugin,vpn,b2b,eshop,blog,annonce,link,seo,proxy,mailing,affiliate\">Devtech</a></div>\n";
        }
        return "";
    }

    public function form($args, $instance) {
        $promoted = $instance[$args["name"]];
        ?>
        <input style="clear: left; float: left; margin-left: 10px;"
               id="<?php echo $args["id"]; ?>" 
               name="<?php echo $args["name"]; ?>" 
               type="checkbox" 
               <?php
               if ($promoted) {
                   echo "checked ";
               }
               ?>
               value="<?php echo $args["exceptedValue"]; ?>" />     
        <label style="float: left;margin-left: 10px;" for="<?php echo $args["id"]; ?>"><?php _e('You can promote us with small link on bottom of page.', 'fix-multiple-redirects'); ?></label>
        <br style="clear: both;">
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['FIXMTPLREDIR_promote'] = $new_instance['FIXMTPLREDIR_promote'];
        update_option("FIXMTPLREDIR_promote", $instance['FIXMTPLREDIR_promote']);
        return $instance;
    }

}
?>
