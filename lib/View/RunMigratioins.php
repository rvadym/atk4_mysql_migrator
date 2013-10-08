<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/8/13
 * Time: 11:17 AM
 * To change this template use File | Settings | File Templates.
 */
namespace atk4_mysql_migrator;
class View_RunMigratioins extends \View {
    function init() {
        parent::init();
        $this->utility = $this->add('atk4_mysql_migrator\Controller_Utility');
        $migrator = $this->migrator = $this->add('atk4_mysql_migrator\Controller_Migrator');

        $form = $this->add('Form');
        $form->addField('Line','dump_name')->set(true);
        $form->addField('CheckBox','create_dump')->set(true);
        $form->addSubmit('Run All Migrations');
        $form->onSubmit(function($form) use ($migrator) {
            if ($form->get('create_dump')==true) {
                $form->set('dump_name',trim($form->get('dump_name')));
                if ($form->get('dump_name')=='') {
                    $form->js()->atk4_form('fieldError','dump_name','required')->execute();exit;
                }
                $migrator->createDump($form->get('dump_name'));
                exit('ddd');
            }
            $migrator->executeAll();
            $form->js()->univ()->successMessage('Done')->closeDialog()->execute();exit;
        });
    }
}