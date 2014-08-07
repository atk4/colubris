<?php
class page_api_right extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_User_Right');
    }
    function page_getAvailableRights(){
        try{

            echo json_encode([
                'result' => 'success',
                'data'   => Model_User_Right::$available_rights,
            ]);
            exit();
        }catch(Exception $e){
            echo json_encode([
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ]);
            exit();
        }
    }
}