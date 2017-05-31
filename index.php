<?php

require __DIR__.'/vendor/autoload.php';

use Gishiki\Core\Route;
use Gishiki\HttpKernel\Request;
use Gishiki\HttpKernel\Response;
use Gishiki\Algorithms\Collections\SerializableCollection;
use Gishiki\Gishiki;


Route::get("/", function (Request &$request, Response &$response) {
    $result = new SerializableCollection([
        "disclaimer" => "This service is not intendended to be really used except for trial! ".
                        "This service MUST not be used to force ANY password!".
                        "This service goes offline after a certain number of usage time, so don't rely on it!! (come on! if you are searching shit like that find something better to do)!".
                        "What you do is your responsibility!".
                        "Really: find a something better to do!",
        "framework" => "https://github.com/NeroReflex/Gishiki",
        "info" => "This is a free service used to demostrate the Gishiki framework",
        "list" => "https://github.com/danielmiessler/SecLists/tree/master/Passwords",
        "source" => "https://github.com/NeroReflex/HashKraken",
        "time" => time()
    ]);

    //send the response to the client
    $response->setSerializedBody($result);
});


Route::any(Route::NOT_FOUND, function (Request &$request, Response &$response) {
    $result = new SerializableCollection([
        "error" => "Not Found",
        "time" => time()
    ]);

    //send the response to the client
    $response->setSerializedBody($result);
});

Route::get("/setup", "HashedPassword->setup");
Route::get("/hash/{word:string}", "HashedPassword->hash");
Route::get("/hash", "HashedPassword->hash");
Route::get("/reverse/{hash:string}", "HashedPassword->reverse");
//this triggers the framework execution
Gishiki::run();
