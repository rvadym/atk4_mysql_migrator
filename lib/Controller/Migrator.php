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
    function getStatuses() {
        $st_json = $this->getCurrentPoint()->get('statuses_json');
        return json_decode($st_json);
    }
    function setStatuses($arr) {
        $this->getCurrentPoint()->set('statuses_json', json_encode($arr))->save();
    }
}