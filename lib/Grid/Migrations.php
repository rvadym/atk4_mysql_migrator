<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 10/4/13
 * Time: 8:50 PM
 * To change this template use File | Settings | File Templates.
 */
namespace atk4_mysql_migrator;
class Grid_Migrations extends \Grid {
    function init() {
        parent::init();
        $this->utility = $this->add('atk4_mysql_migrator\Controller_Utility');
        $this->migrator = $this->add('atk4_mysql_migrator\Controller_Migrator');

        $this->js('reload')->reload();
        $this->addColumn('id');
        $this->addColumn('name');
        $this->addColumn('status');
        $this->addColumn('view');

        $m = $this->add('atk4_mysql_migrator\Model_Migration');
        $arr = $m->getAll();
        $this->setSource($arr);

        $this->statuses = $this->migrator->getStatuses();
        var_dump($this->statuses);
    }
    function formatRow() {
        parent::formatRow();
        $this->current_id = trim($this->current_id);
        $this->current_id = str_replace("\n",'',$this->current_id);

        $this->addViewButton();
        $this->formatStatus();
    }
    private function addViewButton() {
        $view = $this->add('Button','viewbut'.$this->current_id,'content')->set('View');
        $view->js('click')->univ()->frameURL('View Migration',$this->api->url('./view',array('id'=>$this->current_id)));
        $this->current_row_html['view'] = $view->getHTML();
    }
    private function formatStatus() {
        if (strtoupper(trim($this->current_row['status'])) == 'NEW') {
            $this->current_row_html['status'] = '<span style="color:grey">NEW</span>';
        } else if (strtoupper(trim($this->current_row['status'])) == 'OK') {
            $this->current_row_html['status'] = '<span style="color:green">OK</span>';
        } else if (strtoupper(trim($this->current_row['status'])) == 'FAIL') {
            $this->current_row_html['status'] = '<span style="color:red">FAIL</span>';
        }
    }
}