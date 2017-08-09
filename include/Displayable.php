<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Ricina
 */
if (!interface_exists("Displayable")) {
    
    interface Displayable {

        public function display($object=null,$selected_object=null,$attributName="",$attributId="");

        public function form($args, $instance);

        public function update($new_instance, $old_instance);
    }

}
?>
