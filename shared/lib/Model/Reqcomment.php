<?php
class Model_Reqcomment extends Model_Auditable {
	public $table='reqcomment';
	function init(){
		parent::init();//$this->debug();
		$this->hasOne('Requirement');
		$this->hasOne('User')->Caption('Creator');
		$this->addField('text')->type('text')->mandatory('required');

		$attach = $this->add('filestore/Field_Image','file_id')->setModel('Model_Myfile');
		$attach->addThumb();

		$this->addField('created_dts')->Caption('Created At')->sortable(true);

		$this->addField('is_deleted')->type('boolean')->defaultValue('0');
//        $this->addField('deleted_id')->refModel('Model_User');
		$this->hasOne('User','deleted_id');

        $this->addExpression('user_avatar_thumb')->set(function($m,$q){
            return $q->dsql()
                ->table('user')
                ->table('filestore_file')
                ->table('filestore_image')
                ->field('filename')
                ->where('user.id',$q->getField('user_id'))
                ->where('filestore_image.original_file_id=user.avatar_id')
                ->where('filestore_image.thumb_file_id=filestore_file.id')
                ;
        });

		$this->addHooks();
	}

	// ------------------------------------------------------------------------------
	//
	//            HOOKS :: BEGIN
	//
	// ------------------------------------------------------------------------------

	function addHooks() {
//		$this->addHook('beforeDelete', function($m){
//			$m['deleted_id']=$m->api->currentUser()->get('id');
//		});

//		$this->addHook('beforeInsert',function($m,$q){
//			$q->set('user_id',$q->api->auth->model['id']);
//			$q->set('created_dts', $q->expr('now()'));
//		});

//		$this->addHook('beforeSave',function($m){
//			if($m['user_id']>0){
//				if($m->api->auth->model['id']!=$m['user_id']){
//					throw $m
//						->exception('You have no permissions to do this','ValidityCheck')
//						->setField('text');
//				}
//			}
//		});
//		$this->addHook('beforeDelete',function($m){
//			if($m['user_id']>0){
//				if($m->api->auth->model['id']!=$m['user_id']){
//					throw $m
//						->exception('You have no permissions to do this','ValidityCheck');
//				}
//			}
//		});
	}

	function deleted() {
		//$this->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));
		$this->addCondition('is_deleted',true);
		return $this;
	}
	function notDeleted() {
		$this->addCondition('is_deleted',false);
		return $this;
	}
}
