<?php
class page_task extends Page_Functional {
	private $task;
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->app->user_access->canSeeTaskList() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

        $this->api->stickyGet('task_id');

        $mp=$this->add('Model_Project')->notDeleted();
        if($this->api->currentUser()->isDeveloper()){
            $mp->forDeveloper();
        }elseif($this->api->currentUser()->isClient()){
            $mp->forClient();
        }

        $this->task=$this->add('Model_Task');//->debug();
        $this->task->tryLoad($_GET['task_id']);
        if(!$this->task->loaded()){
            throw $this->exception('Task not exist!','Exception_Task');
        }

	    $permission_granted=false;
        foreach($mp->getRows() as $pr){
            if ($pr['id']==$this->task->get('project_id')) $permission_granted=true;
        }
        if(!$permission_granted) throw $this->exception('You cannot see this page','Exception_Denied');

	    $this->addBC();

        $_GET['project_id']=$this->task->get('project_id');
	    $this->task=$this->add('Model_Task_RestrictedUsers');
	    $this->task->forTaskForm();
	    $this->task->load($_GET['task_id']);

	    $this->add('H3')->set('Task details:');
	    $this->add('Form_Task',['model'=>$this->task]);

	    $this->addTaskTime();

	    $this->addComments();

        $this->add('P');
        $v = $this->add('View');

        /*
        // left view
        $left_view = $v->add('View')->setClass('span6 right');
        $left_view->add('H4')->set('Attachments');

        $model=$left_view->add('Model_Attach')->addCondition('task_id',$_GET['task_id']);
        $crud=$left_view->add('CRUD',array(
            'grid_class' => 'Grid_Attachments',
        ));
        $crud->setModel($model,
            array('description','file_id'),
            array('description','file','file_thumb','updated_dts')
        );
*/

        $comments_view = $v->add('View');
        $comments_view->add('H4')->set('Comments');

        $cr=$comments_view->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));

        $m=$comments_view->add('Model_Taskcomment')->notDeleted()
            ->addCondition('task_id',$_GET['task_id']);

        $cr->setModel($m,
            array('text','file_id'),
            array('text','user','file','file_thumb','created_dts')
        );
        if($cr->grid){
            $cr->grid->addClass('zebra bordered');
            //$cr->add_button->setLabel('Add Comment');
            //$cr->grid->setFormatter('text','text');
            $cr->grid->addFormatter('text','wrap');
            $cr->grid->addPaginator(10);
        }
        if($_GET['delete']){
            $comment=$this->add('Model_Taskcomment')->notDeleted()->load($_GET['delete']);
            $comment->delete();
            $cr->js()->reload()->execute();
        }
    }
	protected function addComments(){
		$v = $this->add('View');
		$comments_view = $v->add('View');
		$comments_view->add('H4')->set('Comments');

		$cr=$comments_view->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));

		$m=$comments_view->add('Model_Taskcomment')->notDeleted()
			->addCondition('task_id',$_GET['task_id']);

		$cr->setModel($m,
			array('text','file_id'),
			array('text','user','file','file_thumb','created_dts')
		);
		if($cr->grid){
			$cr->grid->addClass('zebra bordered');
			$cr->add_button->setLabel('Add Comment');
			//$cr->grid->setFormatter('text','text');
			$cr->grid->addFormatter('text','wrap');
			$cr->grid->addPaginator(10);
		}
		if($_GET['delete']){
			$comment=$this->add('Model_Taskcomment')->notDeleted()->load($_GET['delete']);
			$comment->delete();
			$cr->js()->reload()->execute();
		}
	}
	protected function addTaskTime(){
		if (!$this->api->currentUser()->isClient()){
			$this->add('H3')->set('Time details:');

			$model=$this->add('Model_TaskTime')->addCondition('task_id',$_GET['task_id']);
			$crud=$this->add('CRUD');
			if ($this->api->auth->model['is_client']){
				$crud->setModel($model,
					array('spent_time','comment','date'),
					array('user','spent_time','comment','date','remove_billing')
				);
			}else{
				$crud->setModel($model,
					array('spent_time','comment','date','remove_billing'),
					array('user','spent_time','comment','date','remove_billing')
				);
			}
			if ($crud->grid){
				$crud->grid->addTotals(array('spent_time'));
				$crud->grid->addClass('zebra bordered');
				$crud->add_button->setLabel('Add Time');
				$crud->grid->addPaginator(20);
			}

			if ($_GET['reload_view']) {
				$this->js(true)->closest(".ui-dialog")->on("dialogbeforeclose",
					$this->js(null,'function(event, ui){
                                '.$this->js()->_selector('#'.$_GET['reload_view'])->trigger('reload').'
                            }
                    ')
				);
			}
		}
	}
	protected function addBC(){
		$this->add('x_bread_crumb/View_BC',array(
			'routes' => array(
				0 => array(
					'name' => 'Home',
				),
				1 => array(
					'name' => 'Tasks',
					'url' => $this->api->url('tasks',array(
							'project_id'=>$this->api->recall('project_id'),
							'quote_id'=>$this->api->recall('quote_id'),
							'requirement_id'=>$this->api->recall('requirement_id'),
							'status'=>$this->api->recall('status'),
							'assigned_id'=>$this->api->recall('assigned_id'),
						)),
				),
				2 => array(
					'name' => 'Task',
					'url' => 'task',
				),
			)
		));
	}

}