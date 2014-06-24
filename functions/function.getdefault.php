<?php

    ///////////////////////////////////////////////////////////////////////////////////////////////
    // FUNCTION : getdefault()
    // Purpose: Figures out what value to set default in forms based on the vlaue of $item
    ///////////////////////////////////////////////////////////////////////////////////////////////

    function getdefault($item, $value, $type) {
        if ($item == $value) {
            if ($type == 'check') {
                return ' checked="checked"';
            } elseif ($type == 'select') {
                return ' selected="selected"';
            }
        } else {
            return '';
        }
    }
?>