<?php

if (!class_exists("FilterCheckbox")) {

    class FilterCheckbox implements Displayable {

        private function get_filters_checkboxes($filters, $selected_filters = null, $attributName = "filters", $attributId = "filters") {
            $ou = "";
            if ($filters != null) {
                foreach ($filters as $filter) {
                    if ($selected_filters != null) {
                        $checked = in_array($filter, $selected_filters) ? "checked" : "";
                    }
                    $ou .= "<input style=\"clear: left; float: left; margin-left: 10px;\" " . $checked . " type=\"checkbox\" name=\"" . $attributName . "[]\" value=\"" . $filter . "\"  id=\"" . $attributId . "_" . $filter . "[]\" />\n";
                    $ou .= "<label style=\"float: left;margin-left: 10px;\" for=\"" . $attributId . "_" . $filter . "\">" . $filter . "</label>\n";
                }
            }
            return $ou;
        }

        public function display($filters = null, $selected_filters = null, $attributName = "filters", $attributId = "filters") {
            echo $this->get_filters_checkboxes($filters, $selected_filters, $attributName, $attributId);
        }

        public function form($args, $instance) {
            
        }

        public function update($new_instance, $old_instance) {
            
        }

    }

}
?>
