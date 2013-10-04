<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/3/13
 * Time: 8:44 PM
 * To change this template use File | Settings | File Templates.
 */
namespace atk4_mysql_migrator;
class Model_Migration extends Model_Abstract_M {
    public $type = 'migration';
    function init() {
        parent::init();
        $this->addField('name');
        $this->addField('description');
        $this->addField('sql');
    }

    function createMigration() {
        $path = $this->utility->getMigrationsDirPath().$this->generateFileName();
        var_dump($path);
    }

    private function generateFileName() {
        return $this->api->short_name.'-'.time();
    }
}