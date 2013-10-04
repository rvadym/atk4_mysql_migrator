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
    public $id      = null;
    public $type    = null;
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

    private function _load($path) {
        var_dump($path);
    }



    function load($filename){
        if (!$this->type) throw $this->exception();
        switch ($this->type) {
            case 'migration':
                $this->_load($this->utility->getMigrationsDirPath().$filename);
                break;
            case 'dump':
                $this->_load($this->utility->getDumpsDirPath().$filename);
                break;
            default:
                throw $this->exception('Don\'t know how to load type '.$this->type);
        }
        return $this;
    }

    private function _save($path) {
        var_dump($path);
    }
    function save($filename){
        if (!$this->type) throw $this->exception();
        switch ($this->type) {
            case 'migration':
                $this->_save($this->utility->getMigrationsDirPath().$filename);
                break;
            case 'dump':
                $this->_save($this->utility->getDumpsDirPath().$filename);
                break;
            default:
                throw $this->exception('Don\'t know how to save type '.$this->type);
        }
        return $this;
    }
}