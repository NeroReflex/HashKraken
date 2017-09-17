#!/usr/bin/env php
<?php
include __DIR__.'/vendor/autoload.php';

global $argv;
global $argc;

ini_set('memory_limit', '-1');
set_time_limit(0);

use Gishiki\Gishiki;
use Gishiki\Core\Environment;
use Gishiki\CLI\Console;
use Gishiki\Algorithms\Collections\SerializableCollection;
use Gishiki\CLI\ConsoleColor;
use Gishiki\Algorithms\Collections\DeserializationException;

Gishiki::initialize();

if ($argc < 3) {
    Console::setForegroundColor(ConsoleColor::TEXT_RED);
    Console::setBackgroundColor(ConsoleColor::BACKGROUND_WHITE);
    Console::writeLine("Usage: php dictionary.php localhost:8080 dictionary.txt");

    exit(-1);
}

$fileStr = $argv[2];
$urlStr = trim($argv[1], " \r\n\t\0\x0B/");

$handle = fopen($fileStr, "r");

if (!$handle) {
    Console::setForegroundColor(ConsoleColor::TEXT_RED);
    Console::setBackgroundColor(ConsoleColor::BACKGROUND_WHITE);
    Console::writeLine("Error while opening the file ".$argv[2]." Halt.");
    exit(-1);
}

$i = 0;
while (($line = fgets($handle)) !== false) {
    $messageEncoded = new SerializableCollection(['message' => trim($line)]);

    try {
         // create curl resource
         $ch = curl_init();

         // set url
         curl_setopt($ch, CURLOPT_URL, $urlStr."/hash");

         // set method
         curl_setopt($ch, CURLOPT_POST, 1);

         // set content
         curl_setopt($ch, CURLOPT_POSTFIELDS, $messageEncoded->serialize());
         curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);

         // return response instead of printing.
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

         // $output contains the output string
         $output = curl_exec($ch);

         // close curl resource to free up system resources
         curl_close($ch);

         // deserialize the result
         $decodedResult = SerializableCollection::deserialize($output);

         Console::writeLine($decodedResult->get('message')." => sha256:".$decodedResult->get('sha256'));

    } catch (DeserializationException $ex) {


         Console::writeLine($messageEncoded->get('message')." => failed");
    }

    $i++;
}

Console::setForegroundColor(ConsoleColor::TEXT_GREEN);
Console::setBackgroundColor(ConsoleColor::BACKGROUND_WHITE);
Console::writeLine("Hashed ".$i." strings. Done.");

fclose($handle);
