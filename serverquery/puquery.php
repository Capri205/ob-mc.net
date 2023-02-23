<?php
	
	// For the sake of this example
//	Header( 'Content-Type: text/plain' );
//	Header( 'X-Content-Type-Options: nosniff' );

        // check IP address of requester
	$remoteip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
	if (!preg_match('/^192\.168\.1\.1$/', $remoteip)){
		return;
 	}

	// validate input
        $id = $_POST['id'];
        if($id != "MCMonitor") {
                echo "Invalid request";
		return;
        }

	// setup and create a new tracking file position data file if one doesn't exist

	$tfpos = 0;
	$tfposdata = array();
	$tfposfile = "tracker.pos";
	$tfdatfile = "/overviewer/serverquery/tracker.dat";
	$tfdatartn = array();
	$tfposfh = null;
	if (!file_exists($tfposfile)) {
		$tfposfh = fopen($tfposfile, "w");
		$tfposdata = array('MCMonitor' => 0);
		fwrite($tfposfh, json_encode($tfposdata));
		fclose($tfposfh);
	}

	// retrieve tracking data since last update for each id
	if (!file_exists($tfdatfile)) {
		return;
	}
	$jsondata = file_get_contents($tfposfile);
	$tfposdata = json_decode($jsondata);
	foreach ($tfposdata as $key => &$value) {

			// open our tracker for reading and get the number of lines	
			$tfdatfh = new SplFileObject($tfdatfile, 'r');
//			$tfdatfh->seek(PHP_INT_MAX);
//			$lastline = ($tfdatfh->key());
//			$tfdatfh->rewind();
//			echo "debug - last row " . $lastline . "<br>";

			// read lines from our id position to the end if not the same
			$tfdatfh->seek($value - 1);
			while (!$tfdatfh->eof()) {
				if ( $tfdatfh->current() != "" ) {
					array_push($tfdatartn, $tfdatfh->current());
				}
				$tfdatfh->next();
			}
			// update id with new key value
			$value = $tfdatfh->key();
			$tfdatfh = null;
	}
	unset($value);

	// save our postion tracker
	$tfposfh = fopen($tfposfile, "w");
	fwrite($tfposfh, json_encode($tfposdata));
	fclose($tfposfh);

	// return our data as json array
	print_r(json_encode($tfdatartn));
	return;
?>
