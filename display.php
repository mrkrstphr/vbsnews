<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // display.php - this file is used to display the news
    ////////////////////////////////////////////////////////////////////////////
    // IMPORTANT:
    // In order for the script to display news properly, it is required that
    // the following variable be set to the absolute path of the vbsNews
    // directory, including a TRAILING SLASH (see example):

    $CONFIG['absolute_path'] = '/home/you_site/public_html/news/';

    // If you are unsure what this value is, contact your system administrator
    // or webhosting provider.
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////
    // Include the configuration file:
    $skip_cookies = true;
    include $CONFIG['absolute_path'] . 'config.php';

    ////////////////////////////////////////////////////////////////////////////
    // RECOMMENDED FUNCTIONS:
    // These are the recommended functions for displaying vbsNews items. For
    // more control (and advanced users), individual functions are provided
    // below.
    ////////////////////////////////////////////////////////////////////////////
    // EXECUTE - This is the generalized function that runs everything
    ////////////////////////////////////////////////////////////////////////////
    function execute($cat = '') {
        global $CONFIG;

        // Do the requested action:
        if (isset($_REQUEST['action'])) {
            switch ($_REQUEST['action']) {
                case 'archives':
                    include $CONFIG['absolute_path'] . 'archives.php';
                    archivesmain($cat); break;
                case 'search': //search results
                    _search($cat = ''); break;
                case 'comments':
                    include $CONFIG['absolute_path'] . 'comments.php';
                    _comments(); break;
                case 'showitem':
                    showitem(); break;

                default: //news
                    _news($cat);
            }
        } else {
            _news($cat);
        }
    }

    
    ////////////////////////////////////////////////////////////////////////////
    // REQUIRED FUNCTIONS:
    // These functions can be called individually to display news, comments,
    // archives, etc, for more control, if needed.
    ////////////////////////////////////////////////////////////////////////////
    // _NEWS - Show news according to settings
    //////////////////////////////////////////////////////////////////////////// 
    function _news($cat = '') {
        global $CONFIG;

        // Figure out where to start:
        $start = (isset($_REQUEST['s'])) ? $_REQUEST['s'] : 1;

        // Single out a category?:
        if ($CONFIG['enablecats'] == 'yes') {
            if (is_array($cat)) {
                // We have an array of categories to use:
                $cat_stuff = "WHERE ";
                // Loop and add each category:
                for ($i = 0; $i < count($cat); $i++) {
                    $cat_stuff .= "cat_name='" . addslashes($cat[$i]) . "' ";

                    if (($i + 1) < count($cat)) {
                        $cat_stuff .= "OR ";
                    }
                }
            } elseif ($cat != '') {
                // We only have one category:
                $cat_stuff = "WHERE cat_name='" . addslashes($cat) . "' ";
            } else {
                // No categories given:
                $cat_stuff = '';
            }
        }        
        
        // Setup the SQL statement:
        if ($cat_stuff == '') {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news";
        } else {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news $cat_stuff";
        }

        $sql .= " ORDER BY postedOn DESC";

        // Create the SQL limit:
        if ($CONFIG['shownews'] == 'all') {
            $limit = " LIMIT " . (($start - 1) * $CONFIG['numitems']) . ", " . $CONFIG['numitems'];
        } else {
            $limit = " LIMIT " . $CONFIG['numitems'];
        }

        // Show the news:
        show_news(($sql . $limit));

        // Create links, if needed::
        if ($CONFIG['shownews'] == 'all') {
            include_once $CONFIG['absolute_path'] . 'functions/function.createlinks.php';

            // Get $_GET values for links:
            $url_parts = '';
            foreach ($_GET as $key => $val) {
                // We don't include the $_GET['s'] because we change it's value:
                if ($key != 's') {
                    if ($url_parts != '') {
                        $url_parts .= ('&' . $key . '=' . $val);
                    } else {
                        $url_parts .= ('?' . $key . '=' . $val);
                    }
                }
            }

            // Setup and create the navigation links:
            if ($cat_stuff != '') {
                $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news $cat_stuff";
            } else {
                $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news";
            }   

            $num = @mysql_num_rows(mysql_query($sql));

            $url_parts .= ($url_parts == '') ? '?' : '&';
            $links = createlinks($num, $CONFIG['numitems'], $start, $url_parts . 's=');

            // Echo the links within the 'pagelinks' template:
            echo str_replace('<links>', $links, gettemplate('pagelinks'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // SHOWITEM - Show an individual news item by ID
    //////////////////////////////////////////////////////////////////////////// 
    function showitem() {
        global $CONFIG;
        
        // If ID is set:
        if (isset($_REQUEST['id'])) {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE id=" . $_REQUEST['id'];
            show_news($sql);
        } else {
            die(displayerror('No news item ID specified.'));
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // HEADERS - Show news headers
    //////////////////////////////////////////////////////////////////////////// 
    function headers($number, $cat = '') {
        global $CONFIG;

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
      
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news $cat_stuff ORDER by postedOn DESC LIMIT " . $number;
        $headers = mysql_query($sql) or die(displayerror(mysql_error()));

        if ($header = mysql_fetch_array($headers)) {
            do {
                $hitem = gettemplate('header');
                
                $hitem = str_replace('<id>', $header['id'], $hitem);
                $hitem = str_replace('<subject>', stripslashes(htmlspecialchars($header['subject'])), $hitem);

                // Get user information (if needed):
                if (strpos($hitem, '<user>') !== false) {
                    $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE id=" . $header['uID'];
                    $users = mysql_query($sql) or die(mysql_error());

                    if ($user = mysql_fetch_array($users)) {
                        $hitem = str_replace('<user>', htmlspecialchars($user['usn']), $hitem);
                    } else {
                        $hitem = str_replace('<user>', 'Uknown User', $hitem);
                    }
                }
                
                echo $hitem;
            } while($header = mysql_fetch_array($headers));       
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // _ARCHIVES - Call the news archives functions:
    ////////////////////////////////////////////////////////////////////////////
    function _archives($cat = '') {
        global $CONFIG;

        include $CONFIG['absolute_path'] . 'archives.php';
        archivesmain($cat);
    }

    ////////////////////////////////////////////////////////////////////////////
    // _SEARCH - Search for news items:
    ////////////////////////////////////////////////////////////////////////////
    function _search($cat = '') {
        global $CONFIG;

        // See if we have a category in the query string:
        if ($cat == '') {
            if (isset($_REQUEST['cat'])) {
                $cat = $_REQUEST['cat'];
            }
        }

        if (isset($_REQUEST['s'])) {
            $start = $_REQUEST['s'];
        } else {
            $start = 1;
        }

        if ($CONFIG['enablecats'] == 'yes') {
            if (is_array($cat)) {
                $cat_stuff = 'AND (';
                for ($i = 0; $i < count($cat); $i++) {
                    $cat_stuff .= "cat_name='" . addslashes($cat[$i]) . "' ";

                    if (($i + 1) < count($cat)) {
                        $cat_stuff .= "OR ";
                    }
                }

                $cat_stuff .= ')';
            } elseif ($cat != '') {
                $cat_stuff = "cat_name='" . addslashes($cat) . "'";
            } else {
                $cat_stuff = '';
            }
        }

        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE body LIKE '%" . $_REQUEST['search_query'] . "%' $cat_stuff";
        $sql .= " ORDER BY postedOn DESC LIMIT " . (($start - 1) * $CONFIG['numitems']) . ", " . $CONFIG['numitems'];

        show_news($sql);

        $num = mysql_num_rows(mysql_query("SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE body LIKE '%" . $_REQUEST['search_query'] . "%' $cat_stuff"));

        require 'functions/function.createlinks.php';

        $link = '?action=search&search_query=' . $_REQUEST['search_query'];

        if ($cat != '') {
            if (is_array($cat)) {
                for ($i = 0; $i < count($cat); $i++) {
                    $link .= '&cat[]=' . $cat[$i];
                }
            } else {
                $link .= '&cat=' . $cat;
            }
        }
            
        $link .= '&s=';
        $links = createlinks($num, $CONFIG['numitems'], $start, $link);

        echo str_replace('<links>', $links, gettemplate('pagelinks'));
    }

    ////////////////////////////////////////////////////////////////////////////
    // SHOW_NEWS - show news items based on SQL query:
    ////////////////////////////////////////////////////////////////////////////
    function show_news($sql) {
        global $CONFIG;
        
        // Get the news items from the database:
        $posts = mysql_query($sql) or die(displayerror(mysql_error()));
        if ($post = mysql_fetch_array($posts)) {
            include_once $CONFIG['absolute_path'] . '/functions/function.formatmsg.php';

            // Loop the news items:
            $lastDate = ''; $template = ''; $lastCat = '';
            do {
                // Get news template or individual category template (if needed):
                if ($CONFIG['enablecats'] == 'yes') {
                    if ($post['cat_name'] != $lastCat) {
                        // We have a different category, so get the new template:
                        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "cats WHERE key_name='" . $post['cat_name'] . "'";
                        $cats = mysql_query($sql) or die(displayerror(mysql_error()));

                        // Get template information:
                        if ($cat = mysql_fetch_array($cats)) {
                            if ($cat['templateName'] != '') {
                                // Set template:
                                $template = gettemplate($cat['templateName']);
                            } else {
                                // No custom template, go with default:
                                $template = gettemplate('news');
                            }
                        }
                    }

                    // Setup last category information (saves on SQL calls):
                    $lastCat = $post['cat_name'];
                }

                if ($template == '') {
                        // If we don't yet have the template, get it:
                        $template = gettemplate('news');
                    }
               
                // Parse news template:
                $newspost = $template;
                $newspost = '<a name="' . $post['id'] . '"></a>' . $newspost;
                $newspost = str_replace('<subject>', stripslashes(htmlspecialchars($post['subject'])), $newspost);
                $newspost = str_replace('<id>', $post['id'], $newspost);
                $newspost = str_replace('<datetime>', date($CONFIG['timeformat'], strtotime($post['postedOn'])), $newspost);
                $newspost = str_replace('<item>', formatmsg($post['body']), $newspost);

                // Get user info:
                $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE id=" . $post['uID'];
                $users = mysql_query($sql) or die(displayerror(mysql_error()));
                if ($user = mysql_fetch_array($users)) {
                    // Show email (if allowed):
                    if ($user['hidemail'] == 'no') {
                        $usertag = "<a href=\"mailto: " . $user['email'] . "\">" . htmlspecialchars($user['usn']) . "</a>";
                    } else {
                        $usertag = htmlspecialchars($user['usn']);
                    }
                    // Replace email:
                    $newspost = str_replace('<user>', $usertag, $newspost);
                } else {
                    // Unknown user?:
                    $newspost = str_replace('<user>', 'Uknown User', $newspost);
                }

                // If comments are allowed:
                $tag = '/\<comments(.*)url\="(.*)"(.*)display\="(.*)"(.*)\>/siU';
                if ($CONFIG['allowcomments'] == 'yes') {

                    // Build SQL query to find category:
                    $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "cats WHERE key_name='" . $post['cat_name'] . "'";
                    $cats = mysql_query($sql) or die(displayerror(mysql_error()));
                    if ($cat = mysql_fetch_array($cats)) {
                        // Setup comment information:
                        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "comments WHERE par_id='" . $post['id'] . "'";
                        $num_comments = @mysql_num_rows(mysql_query($sql));
                        $newspost = str_replace('<num>', $num_comments, $newspost);

                        // Erase the comments tag:
                        $r = '<a href="\\2"\\1\\3\\5>\\4</a>';
                        $newspost = preg_replace($tag, $r, $newspost);
                    }
                } else {
                    // Erase the comments tag:
                    $newspost = preg_replace($tag, '', $newspost);
                }

                // Output the news item:
                echo $newspost;
            } while($post = mysql_fetch_array($posts));
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // GETTEMPLATE - Retrieve template information from database:
    ////////////////////////////////////////////////////////////////////////////
    function gettemplate($name) {
        global $CONFIG;

        // GET TEMPLATE INFORMATION:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "templates WHERE name='" . $name . "'";
        $templates = mysql_query($sql) or die(displayerror(mysql_error()));

        if ($template = mysql_fetch_array($templates)) {
            return stripslashes($template['template']);
        } else {
            return '';
        }
        
        mysql_free_result($templates);
    }

    ////////////////////////////////////////////////////////////////////////////
    // DISPLAYERROR - Show an error message
    ////////////////////////////////////////////////////////////////////////////
    function displayerror($error) {
        global $CONFIG;

        $disp_error = gettemplate('display_error');
        $disp_error = str_replace('<error>', $error, $disp_error);

        echo $disp_error;
    }
?>