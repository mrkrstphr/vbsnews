<?php

    ///////////////////////////////////////////////////////////////////////////////////////////////
    // FUNCTION : validateemail()
    // Purpose: basic validate for user emails
    ///////////////////////////////////////////////////////////////////////////////////////////////

    function validateemail($email) {
        if (preg_match('/(.*)@(.*).(.*)/i', $email) == 0) {
            return false;
        } else {
            return true;
        }
    }
?>