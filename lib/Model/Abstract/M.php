<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/4/13
 * Time: 4:16 PM
 * To change this template use File | Settings | File Templates.
 */
namespace atk4_mysql_migrator;
abstract class Model_Abstract_M extends \AbstractModel {
    public $type    = null;
    private $id     = null;
    private $fields = array();
    function init() {
        parent::init();
        $this->utility = $this->add('atk4_mysql_migrator\Controller_Utility');
    }
    function addField($name) {
        return $this->fields[$name] = '';
    }
    function hasField($name) {
        return isset($this->fields[$name]);
    }
    function set($field,$value) {
        if (!$this->hasField($field)) throw $this->exception('Model doesn\'t have field '.$field);
        $this->fields[$field] = $value;
        return $this;
    }
    function get($field=null) {
        if (is_null($field)) return $this->fields;
        if (!$this->hasField($field)) throw $this->exception('Model doesn\'t have field '.$field);
        return $this->fields[$field];
    }
    function getId() {
        if (is_null($this->id)) $this->id = $this->generateFileName();
        return $this->id;
    }
    private function generateFileName() {
        $name = $this->prefix.'-'.time();
        return $name;
    }

    // load
    private function _load($path) {
        $file = $this->utility->readFile($path);
        $arr = $this->utility->parseFile($file,$this->type);
        foreach ($arr as $k=>$v) {
            if ($k != 'id') {
                $this->set($k,$v);
            }
        }
    }
    function load($filename){
        $secure_filename = $this->makeFilenameSecure($filename);
        if (!$this->type) throw $this->exception('type is required');
        $this->_load($this->utility->getDirPathByType($this->type)/*.$this->getId()*/.$secure_filename);
        return $this;
    }
    function makeFilenameSecure($filename) {
        return str_replace(array('/','\\','..','.','php'),'', trim($filename));
    }

    // save
    private function _save($path) {
        $file_text = $this->configureFileText();
        $this->utility->createFile($path,$file_text);
    }
    function save(){
        if (!$this->type) throw $this->exception('type is required');
        $this->_save($this->utility->getDirPathByType($this->type).$this->getId());
        return $this;
    }

    // get all
    function getAll() {
        return $this->utility->getAllFiles($this->type);
    }

    function configureFileText() {
        throw $this->exception('Redefine this function in your model class');
    }
}