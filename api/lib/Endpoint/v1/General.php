<?php
/**
 * Created by Vadym Radvansky
 * Date: 8/4/14 4:25 PM
 */
class Endpoint_v1_General extends Endpoint_REST {


    use Helper_Url;

    protected $m;
    protected $count;
    protected $offset;
    protected $method;

    public $user_id_field = false;

    function init() {
        parent::init();

        $this->getParameter('count') ? $this->count = $this->getParameter('count') : $this->count = 999999999999;
        $this->getParameter('offset') ? $this->offset = $this->getParameter('offset') : $this->offset = 0;
        $this->getParameter('method') ? $this->method = $this->getParameter('method') : $this->method = false;
    }

    /**
     * Parameters:
     * field - which field will be used for search (optional)
     * value - value for the field (optional)
     * count - count rows (optional)
     * offset - offset for query (optional)
     * method - for defining type of search. If empty - strong search, 'rlike', llike', 'alike'
     */
    function get_getByField() {
        try{
            $field = $this->getParameter('field');
            $value = $this->getParameter('value');
            if($field != ''){
                if($this->method){
                    if($this->method == 'rlike') $value = $value . '%';
                    if($this->method == 'llike') $value = '%' . $value;
                    if($this->method == 'alike') $value = '%' . $value . '%';
                    $this->model->addCondition($field,'LIKE',$value);
                }else{
                    $this->model->addCondition($field,$value);
                }
            }
            $this->model->setLimit($this->count,$this->offset);
            $data = $this->model->getRows();
            $this->model->setLimit(999999999,0);
            $total_rows = count($this->model->getRows());
//            var_dump($data);exit;
            echo json_encode([
                'result' => 'success',
                'data'   => $data,
                'total_rows' => $total_rows,
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
    /**
     * Parameters:
     * field1 - which field will be used for search (optional)
     * value1 - value for the field (optional)
     * ...
     * field5 - which field will be used for search (optional)
     * value5 - value for the field (optional)
     * count - count rows (optional)
     * offset - offset for query (optional)
     */
    function get_getByFields() {
        try{
            for($i=1; $i<100; $i++){
                $field = $this->getParameter('field'.$i);
                $value = $this->getParameter('value'.$i);
                if($field != '' && $value != ''){
                    $this->model->addCondition($field,$value);
                }
            }
            $this->model->setLimit($this->count,$this->offset);
            $data = $this->model->getRows();
            $this->model->setLimit(999999999,0);
            $total_rows = count($this->model->getRows());

            echo json_encode([
                'result' => 'success',
                'data'   => $data,
                'total_rows' => $total_rows,
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
    function get_getById() {
        $id = $this->checkGetParameter('id');
        $_GET['field'] = 'id';
        $_GET['value'] = $id;
        $this->get_getByField();
    }
    function get_deleteById() {
        $id = $this->checkGetParameter('id');
        try{
            $this->model->delete($id);
            echo json_encode([
                'result' => 'success',
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
    function get_saveParams(){
        $data = file_get_contents("php://input"); // TODO: not safe data
        $data_arr = @json_decode($data,true);
        if (is_array($data_arr)) {
            $all = array_merge($_REQUEST,$data_arr);
        } else {
            $all = $_REQUEST;
        }
        $id = $this->checkGetParameter('id',true);
        if ($id) {
            $this->model->tryLoad($id);
            if(!$this->model->loaded()){
                echo json_encode([
                    'result' => 'error',
                    'error_message' => 'Record with the id '.$id.' was not found',
                ]);
                exit();
            }
        }
        $this->model->set($all);
        $this->model->save();
        echo json_encode([
            'result' => 'success',
            'data' => $this->model->get(),
        ]);
        exit;
    }




    // just to avoid default actions
    public $allow_list=false;
    public $allow_list_one=false;
    public $allow_add=false;
    public $allow_edit=false;
    public $allow_delete=false;
    function get() {}
    function post($data) {}
    function put($data) {}
    function delete($data) {}
}