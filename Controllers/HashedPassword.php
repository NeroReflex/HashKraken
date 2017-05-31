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
        $deserializedRequest = $this->request->getDeserializedBody();

	    $word = ($this->arguments->has('word')) ? $this->arguments->get('word') : $deserializedRequest->get('message');

        $serializableResponse = new SerializableCollection([
            "message" => $word,
            "sha1" => Algorithms::hash($word, Algorithms::SHA1),
            "sha256" => Algorithms::hash($word, Algorithms::SHA256),
            "sha328" => Algorithms::hash($word, Algorithms::SHA328),
            "sha512" => Algorithms::hash($word, Algorithms::SHA512),
            "md5" => Algorithms::hash($word, Algorithms::MD5),
        ]);

        $connection = DatabaseManager::retrieve('default');
        $presence = $connection->read(
            "hashes",
            SelectionCriteria::select(["message" => $word]),
            ResultModifier::initialize()->limit(1));
        
        if (count($presence) == 0)
            $connection->create("hashes", $serializableResponse);

        $serializableResponse->set("time", time());
        $this->response->setSerializedBody($serializableResponse);
    }

    public function reverse()
    {
        $hash = $this->arguments->get('hash');
        
        $connection = DatabaseManager::retrieve('default');

        //optimize search
        $searchQuery = SelectionCriteria::select()
                ->OrWhere('sha1', FieldRelation::EQUAL, $hash)
                ->OrWhere('sha256', FieldRelation::EQUAL, $hash)
                ->OrWhere('sha328', FieldRelation::EQUAL, $hash)
                ->OrWhere('sha512', FieldRelation::EQUAL, $hash)
                ->OrWhere('md5', FieldRelation::EQUAL, $hash);
        switch (strlen($hash)) {
            case 40:
                $searchQuery = SelectionCriteria::select(['sha1' => $hash]);
                break;

            case 64:
                $searchQuery = SelectionCriteria::select(['sha256' => $hash]);
                break;

            case 96:
                $searchQuery = SelectionCriteria::select(['sha328' => $hash]);
                break;

            case 128:
                $searchQuery = SelectionCriteria::select(['sha512' => $hash]);
                break;

            case 32:
                $searchQuery = SelectionCriteria::select(['md5' => $hash]);
                break;

            default:
                // use the ugly (standard) one
        }

        $result = $connection->read(
            "hashes",
            $searchQuery,
            ResultModifier::initialize()->limit(1));

        $response = new SerializableCollection([
                "found" => count($result),
            ]);

        if (count($result) >= 1) {
            foreach ($result as &$row) {
                unset($row["id"]);
            }
            
            $response->set("collisions", $result);
        }
        
        $response->set('time', time());

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
