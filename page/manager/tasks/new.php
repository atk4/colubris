<?php

class page_manager_tasks_new extends Page {

    function initMainPage() {
    	$s=$this->add('View_Switcher');
    	
    	$this->add('View_TasksNew',array('redirect_to'=>'/manager/tasks','fields'=>array('name','descr_original','estimate','priority','status','requester_id','assigned_id')));
    	
    }
}
