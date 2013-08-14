<?php
class Grid_Reqcomments extends Grid {
    function setModel($model, $actual_fields = UNDEFINED) {
        parent::setModel($model, $actual_fields);
        $this->removeColumn('user');
        $this->removeColumn('file');
        $this->removeColumn('file_thumb');
        $this->removeColumn('created_dts');
    }
    function formatRow() { // var_dump($this->current_row); echo '<hr>';

        // thumb or download link
        if ($this->current_row['file_thumb'] != '') {
            $file = '<a target="_blank" href="'. $this->current_row['file'] .'"><img width="50" src="'.$this->current_row['file_thumb'].'"></a>';
        } else {
            if ($this->current_row['file'] != '') {
                $file = '<a target="_blank" href="'. $this->current_row['file'] .'">download</a>';
            } else {
                $file = '';
            }
        }

        // all fields in one field
        $this->current_row_html['text'] =
                '<strong>'.$this->current_row['user'].':</strong><br>'.
                '<div class="timestamp">'.$this->current_row['created_dts'].'</div>'.
                '<div class="comment radius_10">'.$this->current_row['text'].'</div>'.
                $file
        ;

        parent::formatRow();

        // edit and delete buttons
    	if($this->current_row['user_id']!=$this->api->auth->model['id']){
    		$this->current_row_html['edit']="";
    		$this->current_row_html['delete']="";
    	}
    }
//	function format_text($field){
//       	$this->current_row_html[$field] = '<span style="white-space:wrap;">'.$this->current_row[$field].'</span>';
//       	$this->tdparam[$this->getCurrentIndex()][$field]['style']='white-space: wrap';
//    }
}