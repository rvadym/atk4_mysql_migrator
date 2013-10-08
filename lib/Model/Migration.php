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
    public $type   = 'migration';
    public $prefix = 'JADSC';
    function init() {
        parent::init();
        $this->addField('name');
        $this->addField('description');
        $this->addField('query');
        //$this->addField('new');
    }
    function configureFileText() {
        $text = "";
        $text = $text . "<MIGR_ID>\n".    $this->getId().           "\n</MIGR_ID>\n";
        $text = $text . "<MIGR_NAME>\n".  $this->get("name").       "\n</MIGR_NAME>\n";
        $text = $text . "<MIGR_DESCR>\n". $this->get("description")."\n</MIGR_DESCR>\n";
        $text = $text . "<MIGR_QUERY>\n". $this->get("query").      "\n</MIGR_QUERY>\n";
        //$text = $text . "<MIGR_STATUS>\nnew\n</MIGR_STATUS>\n";
        return $text;
    }
}