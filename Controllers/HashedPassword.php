<?php

use Gishiki\Core\MVC\Controller;
use Gishiki\HttpKernel\Request;
use Gishiki\HttpKernel\Response;
use Gishiki\Algorithms\Collections\SerializableCollection;
use Gishiki\Gishiki;
use Gishiki\Security\Hashing\Algorithms;


final class HashedPassword extends Controller
{
    public function hash()
    {
	$word = $this->arguments->get('word');

        $serializableResponse = new SerializableCollection([
            "message" => $word,
            "sha1" => Algorithms::hash($word, Algorithms::SHA1),
            "sha256" => Algorithms::hash($word, Algorithms::SHA256),
            "sha328" => Algorithms::hash($word, Algorithms::SHA328),
            "sha512" => Algorithms::hash($word, Algorithms::SHA512),
            "md5" => Algorithms::hash($word, Algorithms::MD5),
        ]);
        
        $this->response->setSerializedBody($serializableResponse);
    }
}
