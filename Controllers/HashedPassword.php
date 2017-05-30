<?php

use Gishiki\Core\MVC\Controller;
use Gishiki\HttpKernel\Request;
use Gishiki\HttpKernel\Response;
use Gishiki\Algorithms\Collections\SerializableCollection;
use Gishiki\Gishiki;


final class HashedPassword extends Controller
{
    public function index()
    {
        $serializableResponse = new SerializableCollection([
            "time" => time()
        ]);
        
        $this->response->setSerializedBody($serializableResponse);
    }
}
