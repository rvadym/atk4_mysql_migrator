<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/3/13
 * Time: 8:46 PM
 * To change this template use File | Settings | File Templates.
 */
namespace atk4_mysql_migrator;
class Controller_Migrator extends \AbstractController {
    function init() {
        parent::init();
        $this->utility = $this->add('atk4_mysql_migrator\Controller_Utility');
        $this->pdo = $this->api->db->dbh;
    }
    function query($query) {
        $queries = explode(';',$query);
        foreach ($queries as $q) {
            try {
                $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
                $this->pdo->exec($q);
            } catch (\Exception $e) {
                $error = $this->pdo->errorInfo();
                if ($error[0] != 42000) {//var_dump($error);
                    throw $this->exception($error[2],'atk4_mysql_migrator\Exception_QueryCannotBeExecuted');
                }
            }
        }
    }
    function executeAll() {
        $results = array();
        $statuses = $this->getStatuses();
        $m = $this->add('atk4_mysql_migrator\Model_Migration');
        $all_migrations = $m->getAll();

        $interrupt = false;
        foreach ($all_migrations as $migr) {
            if ($interrupt) continue;
            if (is_object($statuses) && isset($statuses->{$migr['id']})) {
                if ($statuses->{$migr['id']} == 'ok') {
                    $results[$migr['id']] = $statuses->{$migr['id']};
                    continue;
                }
            }
            try {
                $this->query($migr['query']);
                $results[$migr['id']] = 'ok';
            } catch (Exception_QueryCannotBeExecuted $e) {
                $results[$migr['id']] = 'fail';
                $interrupt = true;
            }
        }
        $this->setStatuses($results);
    }
    private $current_point = null;
    function getCurrentPoint() {
        if (is_null($this->current_point)) {
            $this->current_point = $this->add('atk4_mysql_migrator\Model_MigrationPoint')->tryLoadAny();
        }
        return $this->current_point;
    }
    function getStatus($id,$toupper=true) {
        $statuses = $this->getStatuses();
        if (is_object($statuses) && isset($statuses->{$id})) {
            $status = $statuses->{$id};
            if ($toupper) $status = strtoupper(trim($statuses->{$id}));
            return $status;
        }
        return false;
    }
    function getStatuses() {
        $st_json = $this->getCurrentPoint()->get('statuses_json');
        return json_decode($st_json);
    }
    function setStatuses($arr) {
        $this->getCurrentPoint()->set('statuses_json', json_encode($arr))->save();
    }

    function createDump($name) {
        $dump = $this->getDump();
        $m = $this->add('atk4_mysql_migrator/Model_Dump');
        $m->set('name',$name);
        $m->set('query',$dump);
        $m->save();
    }
    private function getDump() {
        $dsn = $this->api->getConfig('dsn');
        $arr = explode('/',$dsn);

        $sDatabase = array_pop($arr);

        $PDO = $this->pdo;
        $PDO->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );

        //$sQuery = "SHOW tables FROM " . $sDatabase;
        $sQuery = "SHOW tables FROM " . $sDatabase;
        $sResult = $PDO->query($sQuery);
        $sData = "\n"
        ."-- PDO SQL Dump --\n"
        ."\n"
        ."SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n"
        ."\n"
        ."--\n"
        ."-- Database: `$sDatabase`\n"
        ."--\n"
        ."\n"
        ."-- --------------------------------------------------------\n"
        ."\n";

        while ($aTable = $sResult->fetch(\PDO::FETCH_ASSOC)) {

          $sTable = $aTable['Tables_in_' . $sDatabase];

          $sQuery = "SHOW CREATE TABLE $sTable";

          $sResult2 = $PDO->query($sQuery);

          $aTableInfo = $sResult2->fetch(\PDO::FETCH_ASSOC);

          $sData .= "\n\n--\n"
          ."-- Tabel structure for table `$sTable`\n"
          ."--\n\n";
          $sData .= $aTableInfo['Create Table'] . ";\n";

          $sData .= "\n\n--\n"
          ."-- Dates of table `$sTable`\n"
          ."--\n\n";


          $sQuery = "SELECT * FROM $sTable\n";

          $sResult3 = $PDO->query($sQuery);

          while ($aRecord = $sResult3->fetch(\PDO::FETCH_ASSOC)) {

            // Insert query per record
            $sData .= "INSERT INTO $sTable VALUES (";
            $sRecord = "";
            foreach( $aRecord as $sField => $sValue ) {
              $sRecord .= "'$sValue',";
            }
            $sData .= substr( $sRecord, 0, -1 );
            $sData .= ");\n";
          }

        }

        return $sData;
    }
}