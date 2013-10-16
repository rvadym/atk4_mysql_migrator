<?php
namespace atk4_mysql_migrator;
class View_RunDump extends \View {
    function init() {
        parent::init();
        $this->utility = $this->add('atk4_mysql_migrator\Controller_Utility');
        $migrator = $this->migrator = $this->add('atk4_mysql_migrator\Controller_Migrator');

        $form = $this->add('Form');
        $form->add('View')->set('Do you want to run dump '.$this->dump.'?');
        $form->dump=$this->dump;
        $form->addSubmit('Run Dump');
        $form->onSubmit(function($form) use ($migrator) {
            $migrator->applyDump($form->dump); //$m->get('query'));
            $form->js()->univ()->successMessage('Done')->closeDialog()->execute();exit;
        });
    }
}