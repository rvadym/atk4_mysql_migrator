<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/4/13
 * Time: 4:21 PM
 * To change this template use File | Settings | File Templates.
 */
class Form_CreateMigration extends \Form {
    function init() {
        parent::init();

        $this->m = $this->add('atk4_mysql_migrator\Model_Migration');

        $this->addField('Line','name');
        $this->addField('Text','description');
        $this->addField('Text','sql');
        $this->addSubmit('Save');
        $this->onSubmit(array($this,'checkSubmitted'));
    }
    function checkSubmitted() {
        $js = array();

        if ($this->get('name')=='') $js[] = $this->js()->atk4_form('fieldError','name',$this->api->_('required'));
        if ($this->get('description')=='') $js[] = $this->js()->atk4_form('fieldError','description',$this->api->_('required'));
        if ($this->get('sql')=='') $js[] = $this->js()->atk4_form('fieldError','sql',$this->api->_('required'));


        if (count($js)) {
            $this->js(null,$js)->execute(); echo 'SPAM PROTECTION :: FORM FIELD ERROR'; exit;
        }

        $this->m
            ->set('name',$this->get('name'))
            ->set('description',$this->get('description'))
            ->set('sql',$this->get('sql'))
            //->save()
        ;

        $this->js()->univ()->closeDialog()->execute(); echo 'SPAM PROTECTION :: FORM SAVED'; exit;
    }
}