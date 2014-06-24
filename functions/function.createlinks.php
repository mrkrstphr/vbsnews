<?php

    ///////////////////////////////////////////////////////////////////////////////////////////////
    // FUNCTION : createlinks()
    // Purpose: Creates navigation links for pages
    ///////////////////////////////////////////////////////////////////////////////////////////////

    function createlinks($num, $len, $start, $link) {
		// Total pages we need:
        $totalpages = floor($num / $len);
        if ($num % $len != 0) {
            $totalpages++;
        }

        $INFO['totalpages'] = $totalpages;
        if ($totalpages > 1) {    
            // Setup info:
            //$INFO['current'] = $current;
            $INFO['len'] = $len;
            $INFO['link'] = $link;
            $INFO['start'] = $start;

            $links = "Pages ($totalpages):  [ ";

            // Create previous link
            if ($start != 1) {
                if ($start < 3 && $totalpages > 3) {
                    $links .= do_loop(1, 3, $INFO);
                } else {
                    $previouss = $start - 1;

                    if ($start >= 3 && $totalpages > 3) {
				        $links .= "<a href=\"$link$previouss\">&#0171;</a> ... ";
                    }

                    if ($start + 1 < $totalpages) {
                        $links .= do_loop($start - 1, $start + 1, $INFO);
                    } elseif ($start + 1 == $totalpages) {
                        $links .= do_loop($start - 1, $start + 1, $INFO);
                    } else {
                        if ($totalpages == 2 && $start == $totalpages) {
                            $links .= do_loop(($start - 1), $totalpages, $INFO);
                        } else {
                            $links .= do_loop(($start - 2), $totalpages, $INFO);
                        }
                    }
                }
            } else {
                if ($totalpages < 3) {
                    $links .= do_loop(1, $totalpages, $INFO);
                } else {
                    $links .= do_loop(1, 3, $INFO);
                }
            }

			// Create next link:
			if ($start != $totalpages && ($start +1) != $totalpages && $totalpages > 3) {
				$nexts = $start + 1;
				$links .= " ... <a href=\"$link$nexts\">&#0187;</a>";
			}

			$links .= " ]";

            return $links;
        }
	}

    ///////////////////////////////////////////////////////////////////////////////////////////////
    // DO_LOOP - Used in createlinks() function:
    ///////////////////////////////////////////////////////////////////////////////////////////////
    function do_loop($start, $end, $INFO) {

        $links = '';
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $INFO['start']) {

                $links .= " <b>$i</b> ";

            } elseif ($i < $INFO['start']) {

                $previouss = $INFO['start'] - ($INFO['len'] * ($INFO['start'] - $i));
                $links .= '<a href="' . $INFO['link'] . $i . '">' . $i . '</a> ';

            } elseif ($i > $INFO['start']) {

                $nexts = ($INFO['len'] * ($i - 1));
                $links .= '<a href="' . $INFO['link'] . $i . '">' . $i . '</a> ';

            }
        }

        return $links;
    }
?>