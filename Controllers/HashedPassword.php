<?php

use Gishiki\Core\MVC\Controller;
use Gishiki\HttpKernel\Request;
use Gishiki\HttpKernel\Response;
use Gishiki\Algorithms\Collections\SerializableCollection;
use Gishiki\Gishiki;
use Gishiki\Security\Hashing\Algorithms;
use Gishiki\Database\Schema\Table;
use Gishiki\Database\Schema\Column;
use Gishiki\Database\Schema\ColumnType;
use Gishiki\Database\Runtime\SelectionCriteria;
use Gishiki\Database\Runtime\FieldRelation;
use Gishiki\Database\Runtime\ResultModifier;
use Gishiki\Database\DatabaseManager;
use Gishiki\Database\DatabaseException;

final class HashedPassword extends Controller
{
    public function hash()
    {
	    $word = $this->arguments->get('word');

        $serializableResponse = new SerializableCollection([
            "time" => time(),
            "message" => $word,
            "sha1" => Algorithms::hash($word, Algorithms::SHA1),
            "sha256" => Algorithms::hash($word, Algorithms::SHA256),
            "sha328" => Algorithms::hash($word, Algorithms::SHA328),
            "sha512" => Algorithms::hash($word, Algorithms::SHA512),
            "md5" => Algorithms::hash($word, Algorithms::MD5),
        ]);

        $this->response->setSerializedBody($serializableResponse);
        
        $connection = DatabaseManager::retrieve('default');
        $presence = $connection->read(
            "hashes",
            SelectionCriteria::select(["message" => $word]),
            ResultModifier::initialize()->limit(1));
        
        if (count($presence) == 0) {
            // it is useless to save the time
            $serializableResponse->remove("time");
            $connection->create("hashes", $serializableResponse);
        }        
    }

    public function reverse()
    {
        $hash = $this->arguments->get('hash');
        
        $connection = DatabaseManager::retrieve('default');
        $result = $connection->read(
            "hashes",
            SelectionCriteria::select()
                ->OrWhere('sha1', FieldRelation::EQUAL, $hash)
                ->OrWhere('sha256', FieldRelation::EQUAL, $hash)
                ->OrWhere('sha328', FieldRelation::EQUAL, $hash)
                ->OrWhere('sha512', FieldRelation::EQUAL, $hash)
                ->OrWhere('md5', FieldRelation::EQUAL, $hash),
            ResultModifier::initialize()->limit(1));

        $response = new SerializableCollection([
                "found" => count($result),
            ]);

        if (count($result) >= 1) {
            $response->set("message", $result[0]["message"]);
        }
        
        $this->response->setSerializedBody($response);
    }

    public function setup()
    {
        $table = new Table("hashes");

        $idColumn = new Column('id', ColumnType::INTEGER);
        $idColumn->setNotNull(true);
        $idColumn->setAutoIncrement(true);
        $idColumn->setPrimaryKey(true);
        $table->addColumn($idColumn);

        $nameColumn = new Column('message', ColumnType::TEXT);
        $nameColumn->setNotNull(true);
        $table->addColumn($nameColumn);

        $surnameColumn = new Column('sha1', ColumnType::TEXT);
        $surnameColumn->setNotNull(true);
        $table->addColumn($surnameColumn);

        $passwordColumn = new Column('sha256', ColumnType::TEXT);
        $passwordColumn->setNotNull(true);
        $table->addColumn($passwordColumn);

        $passwordColumn = new Column('sha328', ColumnType::TEXT);
        $passwordColumn->setNotNull(true);
        $table->addColumn($passwordColumn);

        $passwordColumn = new Column('sha512', ColumnType::TEXT);
        $passwordColumn->setNotNull(true);
        $table->addColumn($passwordColumn);

        $passwordColumn = new Column('md5', ColumnType::TEXT);
        $passwordColumn->setNotNull(true);
        $table->addColumn($passwordColumn);

        $connection = DatabaseManager::retrieve('default');
        $connection->createTable($table);

        $this->response->setSerializedBody(
            new SerializableCollection([
                "time" => time(),
                "result" => "success"
             ]));
    }

}
