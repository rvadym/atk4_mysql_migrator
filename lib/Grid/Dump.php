<?php
namespace atk4_mysql_migrator;

include __DIR__.'/../../vendor/geshi/geshi.php';

class Grid_Dump extends \Grid {
    function init() {
        parent::init();

        $this->api->stickyGet('id');

        $bs = $this->add('ButtonSet',null,'Buttons');
        $this->addRunDumpButton($bs,$this->page_name);

        $this->js('reload')->reload();
        $this->addColumn('field');
        $this->addColumn('value');
        $this->setFormatter('value','wrap');

        $m = $this->add('atk4_mysql_migrator/Model_Dump');
        $m->load($_GET['id']);
        $this->setSource($m->get());
    }
    function formatRow() {
        $this->current_row['field'] = $this->current_row['id'];
        $this->current_row['value'] = trim($this->current_row['name']);
        if ($this->current_row['id'] == 'query') {
            $geshi = new \GeSHi($this->current_row['value'],'mysql');
            $this->current_row_html['value'] = $geshi->parse_code();
        } else {
            $this->current_row_html['value'] = trim($this->current_row['name']);
        }
    }
    private function addRunDumpButton($button_set,$page_name) {
        $b = $button_set->add('Button')->set('Run Dump');
        $this->id=$_GET['id'];
        $b->add('VirtualPage')
            ->bindEvent('Run Dump','click')
            ->set(function($page) use ($page_name){
                $page->add('atk4_mysql_migrator\View_RunDump',array('dump'=>$_GET['id']));
                $page->js(true)->closest(".ui-dialog")->on("dialogbeforeclose",
                    $page->js(null,'function(event, ui){
                             //alert("Text will be changed now!");
                             '. $page->js()->_selector('#'.$page_name.'_migrgrid')->trigger('reload') .';
                         }
                    ')
                );
            });
    }
    function defaultTemplate() {
        return array('view/grid/dump');
    }
}