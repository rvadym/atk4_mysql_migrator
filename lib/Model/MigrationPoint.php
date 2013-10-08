<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/4/13
 * Time: 4:07 PM
 * To change this template use File | Settings | File Templates.
 */
namespace atk4_mysql_migrator;
class Model_MigrationPoint extends \Model_Table {
    public $table = 'atk4_mysql_migrator_migration';
    function init() {
        parent::init();
        $this->utility = $this->add('atk4_mysql_migrator\Controller_Utility');
        $this->addField('name')->mandatory('required');
        $this->addField('statuses_json');
    }
}