<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/3/13
 * Time: 8:44 PM
 * To change this template use File | Settings | File Templates.
 */
namespace atk4_mysql_migrator;
class Model_Dump extends Model_Abstract_M {
    public $type   = 'dump';
    public $prefix = 'JADSC-dump';
    function init() {
        parent::init();
        $this->addField('name');
        $this->addField('query');
    }
    function configureFileText() {
        $text = "";
        $text = $text . "<DUMP_ID>\n".    $this->getId().           "\n</DUMP_ID>\n";
        $text = $text . "<DUMP_NAME>\n".  $this->get("name").       "\n</DUMP_NAME>\n";
        $text = $text . "<DUMP_QUERY>\n". $this->get("query").      "\n</DUMP_QUERY>\n";
        return $text;
    }
}