<?php

class Parser {

    var $debug	= false;
    var $df_param;
    
    function parse_lspci() {

	$results = array();

        if ($_results = execute_program('lspci', '', $this->debug)) {
	    $lines = split("\n", $_results);
	    for ($i = 0, $max = sizeof($lines); $i < $max; $i++) {
		list($addr, $name) = explode(' ', trim($lines[$i]), 2);
		//if (!preg_match('/bridge/i', $name) && !preg_match('/USB/i', $name)) {
		// remove all the version strings
		$name = preg_replace('/\(.*\)/', '', $name);
		// is addr really usefull for this??? i think it's not
		// $results[] = $addr . ' ' . $name;
		$results[] = $name;
		//}
    	    }
	}
	
	if( empty( $results ) ) {
	    return false;
	} else {
	    asort( $results );
	    return $results;
	}
    }

    function parse_pciconf() {
    
        $results = array();

	if($buf = execute_program("pciconf", "-lv", $this->debug)) {
    	    $buf = explode("\n", $buf); $s = 0;
    	    foreach($buf as $line) {
        	if (preg_match("/(.*) = '(.*)'/", $line, $strings)) {
            	    if (trim($strings[1]) == "vendor") {
                	$results[$s] = trim($strings[2]);
            	    } elseif (trim($strings[1]) == "device") {
                	$results[$s] .= " - " . trim($strings[2]);
                	$s++;
            	    }
        	}
    	    }
	}

	if( empty( $results ) ) {
	    return false;
	} else {
	    asort( $results );
	    return $results;
	}
    }

    function parse_filesystems() {

	global $show_bind, $show_inodes;

        $j = 0;

        $df = execute_program('df', '-k' . $this->df_param );
        $df = preg_split("/\n/", $df, -1, PREG_SPLIT_NO_EMPTY);

	if( $show_inodes ) {
	    $df2 = execute_program('df', '-i' . $this->df_param );
	    $df2 = preg_split("/\n/", $df2, -1, PREG_SPLIT_NO_EMPTY);
	}

        $mount = execute_program('mount');
        $mount = preg_split("/\n/", $mount, -1, PREG_SPLIT_NO_EMPTY);
	
	foreach( $df as $df_line) {
	    $df_buf1  = preg_split("/(\%\s)/", $df_line, 2);
	    if( count($df_buf1) != 2) {
		continue;
	    }
	    
	    preg_match("/(.*)(\s+)(([0-9]+)(\s+)([0-9]+)(\s+)([0-9]+)(\s+)([0-9]+)$)/", $df_buf1[0], $df_buf2);
	    $df_buf = array($df_buf2[1], $df_buf2[4], $df_buf2[6], $df_buf2[8], $df_buf2[10], $df_buf1[1]);
	    
	    if( $show_inodes ) {
		preg_match_all("/([0-9]+)%/", $df2[$j + 1], $inode_buf, PREG_SET_ORDER);
	    }
	    
	    if( count($df_buf) == 6 ) {
		if( hide_mount( $df_buf[5] ) ) {
		    continue;
		}
		
		$df_buf[0] = trim( str_replace("\$", "\\$", $df_buf[0] ) );
		$df_buf[5] = trim( $df_buf[5] );
		
		$current = 0;		
		foreach( $mount as $mount_line ) {
		    $current++;
		    
		    if ( preg_match("#" . $df_buf[0] . " on " . $df_buf[5] . " type (.*) \((.*)\)#", $mount_line, $mount_buf) ) {
			$mount_buf[1] .= "," . $mount_buf[2];
	    	    } elseif ( !preg_match("#" . $df_buf[0] . "(.*) on " . $df_buf[5] . " \((.*)\)#", $mount_line, $mount_buf) ) {
		    	continue;
        	    }

		    if ( $show_bind || !stristr($mount_buf[2], "bind")) {
        		$results[$j] = array();
			$results[$j]['disk'] = str_replace( "\\$", "\$", $df_buf[0] );
    			$results[$j]['size'] = $df_buf[1];
		        $results[$j]['used'] = $df_buf[2];
		        $results[$j]['free'] = $df_buf[3];
		        $results[$j]['percent'] = round(($results[$j]['used'] * 100) / $results[$j]['size']);
		        $results[$j]['mount'] = $df_buf[5];
		        $results[$j]['fstype'] = substr( $mount_buf[1], 0, strpos( $mount_buf[1], "," ) );
		        $results[$j]['options'] = substr( $mount_buf[1], strpos( $mount_buf[1], "," ) + 1, strlen( $mount_buf[1] ) );
			if( $show_inodes && isset($inode_buf[ count( $inode_buf ) - 1][1]) ) {
			    $results[$j]['inodes'] = $inode_buf[ count( $inode_buf ) - 1][1];
			}
		        $j++;
			unset( $mount[$current - 1] );
			sort( $mount );
			break;
		    }
		}
	    }
	}
	return $results;
    }

}
?>
