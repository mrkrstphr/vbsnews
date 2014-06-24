<?php

    ///////////////////////////////////////////////////////////////////////////////////////////////
    // FUNCTION : formatmsg()
    // Purpose: Formates given text, replacing [tags] with <html> characters for display output
    ///////////////////////////////////////////////////////////////////////////////////////////////

    function formatmsg($msg) {
        global $CONFIG;

        $msg = stripslashes($msg);

        // Remove html (if not allowed):
        if ($CONFIG['allowhtml'] == 'no') {
            $msg = htmlspecialchars($msg);
        }
        
        // Do tags (if allowed):
        if ($CONFIG['allowtags'] == 'yes') {
            $lststyle = 'style="margin-top: 0px; margin-bottom: 0px;"';

            $tags = array('/\[b\](.*)\[\/b\]/siU', '/\[i\](.*)\[\/i\]/siU', '/\[u\](.*)\[\/u\]/siU',
                    '/\[url\](.*)\[\/url\]/siU', '/\[url=(.*)\](.*)\[\/url\]/siU', '/\[quote\](.*)\[\/quote\](\r\n|)/siU',
                    '/\[color=(.*)\](.*)\[\/color\]/siU', '/\[size=(8|10|14|16)\](.*)\[\/size\]/siU',
                    '/\[align=(.*)\](.*)\[\/align\]/siU', '/\[hr\]/');

            $html = array('<b>\\1</b>', '<i>\\1</i>', '<u>\\1</u>', '<a href="\\1" target="_blank">\\1</a>',
                    '<a href="\\1" target="_blank">\\2</a>', 
                    '<div align="center">
                        <div align="left" style="width: 95%;">
                            <span style="font-size: 8pt; font-weight: bold;">quote:</span><hr>\\1<hr>
                        </div>
                    </div>', '<span style="color: \\1;">\\2</span>', '<span style="font-size: \\1pt;">\\2</span>',
                    '<div align="\\1">\\2</div>', '<hr>');

            $msg = preg_replace($tags, $html, $msg);

            // If image tags are allowed:
            if ($CONFIG['allowimages'] == 'yes') {
                $msg = preg_replace("/\[img\](.*)\[\/img\]/siU", "<img src=\"\\1\" alt=\"\" border=\"0\">", $msg);
                $msg = preg_replace("/\[img\ align=(.*)](.*)\[\/img\]/siU", 
                    "<img src=\"\\2\" alt=\"\" style=\"float: \\1\" border=\"0\">", $msg);
            }
        }

        // Remove long lines and change newlines to <br />:
        $msg = preg_replace("#([^\n\r ?&./<>\"\\-\[\]]{80})#i", "\\1\n", $msg);
        $msg = nl2br($msg);

        if ($CONFIG['allowtags'] == 'yes') {
            require_once 'function.listtag.php';

            $msg = listtag($msg);
        }

        // Return the formatted message:
        return $msg;
    }
?>