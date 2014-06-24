<?php

    ///////////////////////////////////////////////////////////////////////////////////////////////
    // FUNCTION : listtag()
    // Purpose: Special function to format list [tags] into <html> so display is correct
    ///////////////////////////////////////////////////////////////////////////////////////////////

    function listtag($text) {
        $lststyle = 'style="margin-top: 0px; margin-bottom: 0px;"';

        if (preg_match_all('/\[list=(.*)\](.*)\[\/list\]/siU', $text, $match)) {
            $return = preg_replace('/<br \/>/', '', $match[0]);

            $text = str_replace($match[0], $return, $text);
        }
    
        $list_tags = array('/\[list=1\](.*)\[\/list\](\r\n|)/siU', '/\[list=2\](.*)\[\/list\](\r\n|)/siU',
            '/\[list=3\](.*)\[\/list\](\r\n|)/siU', '/\[\*\](.*)\[\/\*\]/isU');
        $list_html = array('<ol ' . $lststyle . '>\\1</ol>', '<ol ' . $lststyle . ' type="a">\\1</ol>', 
            '<ul ' . $lststyle . '>\\1</ul>', '<li>\\1</li>');

        $text = preg_replace($list_tags, $list_html, $text);
        $text = str_replace("</ul><br />", '</ul>', $text);

        return $text;
    }

?>