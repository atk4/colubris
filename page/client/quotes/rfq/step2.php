<?php

class page_client_quotes_rfq_step2 extends Page {
    function page_index(){

    	$this->api->stickyGet('quote_id');


        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Quotes',
                    'url' => 'client/quotes',
                ),
                2 => array(
                    'name' => 'Request for Quotation (requirements)',
                    'url' => 'client/quotes/rfq',
                ),
            )
        ));
    	
        $this->add('P');
        
        $v=$this->add('View')->setClass('left');
        $v->add('H1')->set('Requirements for Quotation');

        $quote=$this->add('Model_Quote')->load($_GET['quote_id']);
        
        // Checking client's permission to this quote
        $project=$this->add('Model_Project')->tryLoad($quote->get('project_id'));
        if( (!$project->loaded()) || ( ($quote->get('status')!="quotation_requested") && ($quote->get('status')!="not_estimated") ) ){
        	$this->api->redirect('/denied');
        }

        $v->add('H4')->set('Quote:');
        $v->add('P')->set('Project - '.$quote->get('project'));
        $v->add('P')->set('User - '.$quote->get('user'));
        $v->add('P')->set('Name - '.$quote->get('name'));
        $v->add('P')->set('General requirement - '.$quote->get('general'));
        
        $v=$this->add('View')->setClass('right');
        $v->add('P')->set('Requirements, which will be added in the future increase estimation.');
        
        $v=$this->add('View')->setClass('clear');
        
        $this->add('H4')->set('Requirements:');
        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);

        $cr = $this->add('CRUD',array('allow_add'=>false));
        $cr->setModel($requirements,
        		array('name','descr','file_id'),
        		array('name','descr','estimate','spent_time','file','user')
        		);
        if($cr->grid){
        	$cr->grid->addColumn('expander','comments');
        	$cr->grid->addFormatter('file','download');
        }        
        
        $this->add('H4')->set('New Requirement:');
        
        $form=$this->add('Form');
        $m=$this->setModel('Model_Requirement');
        $form->setModel($m,array('name','descr','file_id'));
        $form->addSubmit('Save');

        if($form->isSubmitted()){
        	$form->model->set('user_id',$this->api->auth->model['id']);
        	$form->model->set('quote_id',$_GET['quote_id']);
        	$form->update();
        	$this->api->redirect(null);
        }
        
    }

    function page_comments(){
    	$this->api->stickyGET('requirement_id');
    	$cr=$this->add('CRUD',array('allow_del'=>false,'allow_edit'=>false));
    	
    	$m=$this->add('Model_Reqcomment')->addCondition('requirement_id',$_GET['requirement_id']);
    	$cr->setModel($m,
    			array('text'),
    			array('text','user')
    	);
    	if($cr->grid){
    		$cr->add_button->setLabel('Add Comment');
    	}
    	 
    }
}
