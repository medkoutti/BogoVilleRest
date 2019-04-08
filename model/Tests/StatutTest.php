<?php

namespace model;
use PHPUnit\Framework\TestCase;

class StatutTest extends TestCase
{
    public static function setUpBeforeClass(): void{
        parent::setUpBeforeClass();
        global $pdo;
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE " . VoirieProblemeTable::$table_name. "; SET FOREIGN_KEY_CHECKS = 1;");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE " . StatutTable::$TABLE_NAME . "; SET FOREIGN_KEY_CHECKS = 1;");
    }


    public function testPostStatut(){
        global $pdo;
        $statut = new \model\Statut($pdo);
        $postArray = [$statut->getDescription() => "En traitement"];
        $statut->postSomething($postArray, $statut);
        $lastId = $pdo->lastInsertId();
        $statutGet = $statut->getById($lastId, $statut);

        $this->assertEquals($statutGet,(object)array_merge([$statut->getId() => $lastId], $postArray));

        return $lastId;
    }

    /**
     * @depends testPostStatut
     * @param $lastId
     */
    public function testUpdateStatut($lastId){
        global $pdo;
        $statut = new \model\Statut($pdo);
        $statutOne = $statut->getById($lastId, $statut);
        $key = $statut->getDescription();
        $statutOne->$key = "Transmis à Hydro-Québec";
        $statut->updateByID($lastId, [$statut->getDescription() => "Transmis à Hydro-Québec"], $statut);
        $statutTwo = $statut->getById($lastId, $statut);

        $this->assertEquals($statutOne, $statutTwo);
    }

    /**
     * @depends testPostStatut
     * @param $lastId
     */
    public function testGetAll($lastId){
        global $pdo;
        $model = new \model\Statut($pdo);
        $statutOne = $model->getById($lastId, $model);
        $statutPost = [$model->getDescription() => "Relayé au MTQ"];
        $model->postSomething($statutPost, $model);
        $secondId = $pdo->lastInsertId();
        $statutTwo = $model->getById($secondId, $model);

        $array = $model->getAll($model);

        $this->assertEquals($statutOne, $array[0]);
        $this->assertEquals($statutTwo, $array[1]);
    }

    /**
     * @depends testPostStatut
     * @param $lastId
     *
     */
    public function testDeleteById($lastId){
        global $pdo;
        $model = new \model\Statut($pdo);
        $result = $model->deleteById($lastId, $model);
        $hopeFalse = $model->getById($lastId, $model);
        $this->assertTrue($result);
        $this->assertFalse($hopeFalse);

    }

}
