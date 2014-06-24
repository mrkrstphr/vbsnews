<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // archives.php - Archive functions for display.php
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////
    // _ARCHIVES - Handle news archives:
    ////////////////////////////////////////////////////////////////////////////
    function archivesmain($cat = '') {
        global $CONFIG;

        // Make sure archives are turned on:
        if ($CONFIG['oldnews'] == 'delete') {
            die(displayerror('News archiving is currently shut off.'));
        }

        // Call the appropriate function:
        if (isset($_REQUEST['s']) && $_REQUEST['s'] == 'show') {
            archives_show($cat);
        } else {
            archives_list($cat);
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // ARCHIVES_SHOW - Shows individual news archives:
    ////////////////////////////////////////////////////////////////////////////
    function archives_show($cat = '') {
        global $CONFIG;

        // Figure out starting item:
        if (isset($_REQUEST['start'])) {
            $start = $_REQUEST['start'];
        } else {
            $start = 1;
        }

        // Single out a category?:
        if ($CONFIG['enablecats'] == 'yes') {
            if (is_array($cat)) {
                $cat_stuff = "AND (";
                for ($i = 0; $i < count($cat); $i++) {
                    $cat_stuff .= "cat_name='" . addslashes($cat[$i]) . "' ";

                    if (($i + 1) < count($cat)) {
                        $cat_stuff .= "OR ";
                    }
                }
                $cat_stuff .= ")";
            } elseif ($cat != '') {
                $cat_stuff = "AND cat_name='" . addslashes($cat) . "'";
            } else {
                $cat_stuff = '';
            }
        }

        // Build query:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE DATE_FORMAT(postedOn, '%m-%y') = '" . $_REQUEST['date'] . "' ";
        $sql .= "$cat_stuff ORDER by postedOn DESC LIMIT ";
        $sql .= (($start - 1) * $CONFIG['numitems']) . ", " . $CONFIG['numitems'];

        // Show news:
        show_news($sql);

        // Create links:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE DATE_FORMAT(postedOn, '%m-%y') = '" . $_REQUEST['date'] . "' $cat_stuff";
        $num = mysql_num_rows(mysql_query($sql));

        require 'functions/function.createlinks.php';

        $link = '?action=archives&s=show&date='. $_REQUEST['date'] . '&start=';
        $links = createlinks($num, $CONFIG['numitems'], $start, $link);

        echo str_replace('<links>', $links, gettemplate('pagelinks'));
    }

    ////////////////////////////////////////////////////////////////////////////
    // ARCHIVES_LIST - Shows the archive listing:
    ////////////////////////////////////////////////////////////////////////////
    function archives_list($cat = '') {
        global $CONFIG;

        // Single out a category?:
        if ($CONFIG['enablecats'] == 'yes') {
            if (is_array($cat)) {
                $cat_stuff = "WHERE ";
                for ($i = 0; $i < count($cat); $i++) {
                    $cat_stuff .= "cat_name='" . addslashes($cat[$i]) . "' ";

                    if (($i + 1) < count($cat)) {
                        $cat_stuff .= "OR ";
                    }
                }
            } elseif ($cat != '') {
                $cat_stuff = "WHERE cat_name='" . addslashes($cat) . "' ";
            } else {
                $cat_stuff = '';
            }
        }

        $sql = "SELECT DISTINCT DATE_FORMAT(postedOn, '%M 01, %Y') FROM " . $CONFIG['tblPrefix'] . "news $cat_stuff ";
        $sql .= "ORDER by postedOn DESC";

        $archs = mysql_query($sql) or die(displayerror(mysql_error()));
        if ($arc = mysql_fetch_array($archs)) {
            // Get the templates:
            $archives = gettemplate('archives');
            $archive = gettemplate('archiveitem');

            if ($cat_stuff != '') {
                $cat_stuff = str_replace('WHERE', 'AND (', $cat_stuff) . ')';
            }

            $arclinks = '';
            do {
                //$sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news $where DATE_FORMAT(postedOn, '%m-%y') = ";
                //$sql .= "'" . date('m-y', strtotime($arc[0])) . "'";

                $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE DATE_FORMAT(postedOn, '%m-%y') = ";
                $sql .= "'" . date('m-y', strtotime($arc[0])) . "' $cat_stuff";

                $result = mysql_query($sql) or die(displayerror(mysql_error() . '<br />' . $sql, 1));
                $num = mysql_num_rows($result) or die(displayerror(mysql_error()));
                mysql_free_result($result);

                $arclink = "<a href=\"?action=archives&s=show&date=" . date('m-y', strtotime($arc[0])) . "\">";
                $arclink .= date('F Y', strtotime($arc[0])) . "</a>";
                $arclink .= " ($num Items)<br>";

                $arclink = str_replace('<archive_text>', $arclink, $archive);

                $arclinks .= $arclink;
            } while($arc = mysql_fetch_array($archs));

            $archives = str_replace('<archives_listing>', $arclinks, $archives);
            echo $archives;
        } else {
            echo 'No news items were found!';        
        }
    }
?>