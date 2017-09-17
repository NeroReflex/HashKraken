<?php

require __DIR__.'/vendor/autoload.php';

use Gishiki\Core\Route;
use Gishiki\HttpKernel\Request;
use Gishiki\HttpKernel\Response;
use Gishiki\Algorithms\Collections\SerializableCollection;
use Gishiki\Gishiki;


Route::get("/", function (Request &$request, Response &$response) {
    $result = new SerializableCollection([
        "framework" => "https://github.com/NeroReflex/Gishiki",
        "info" => "This is a free service used to demonstrate the Gishiki framework",
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
Route::post("/hash", "HashedPassword->hash");
Route::get("/reverse/{hash:string}", "HashedPassword->reverse");
//this triggers the framework execution
Gishiki::run();
