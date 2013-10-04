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
    }
}