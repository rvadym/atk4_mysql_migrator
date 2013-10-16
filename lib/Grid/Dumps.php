<?php
namespace atk4_mysql_migrator;
class Grid_Dumps extends \Grid {
    function init() {
        parent::init();
        $this->utility = $this->add('atk4_mysql_migrator\Controller_Utility');
        $this->migrator = $this->add('atk4_mysql_migrator\Controller_Migrator');

        $this->js('reload')->reload();
        $this->addColumn('id');
        $this->addColumn('name');
        $this->addColumn('view');

        $m = $this->add('atk4_mysql_migrator\Model_Dump');
        $arr = $m->getAll();
        $this->setSource($arr);
    }
    function formatRow() {
        parent::formatRow();
        $this->current_id = trim($this->current_id);
        $this->current_id = str_replace("\n",'',$this->current_id);

        $this->addViewButton();
    }
    private function addViewButton() {
        $view = $this->add('Button','viewbut'.$this->current_id,'content')->set('View');
        $view->js('click')->univ()->frameURL('View Dump',$this->api->url('./view',array('id'=>$this->current_id)));
        $this->current_row_html['view'] = $view->getHTML();
    }
}