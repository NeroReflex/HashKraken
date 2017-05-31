<?php
ini_set('memory_limit', '-1');
set_time_limit(0);

if ($argc < 3) die("Usage: php dictionary.php localhost:8080 dictionary.txt");

$hashUrl = $argv[1];

$handle = fopen($argv[2], "r");
if ($handle) {
    $i = 0;
    while (($line = fgets($handle)) !== false) {
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, hashUrl."/hash/".urlencode(trim($line)));

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

	$i++;
    }

    echo "aptempted to hash ".$i." words";

    fclose($handle);
} else {
    echo "Error while opening the file ".$argv[2];
}

echo "\n\n";
