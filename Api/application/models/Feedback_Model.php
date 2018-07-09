<?php
 class Feedback_Model extends CI_Model{
     public function __construct()
     {
         parent::__construct();
         $this->load->database();

     }
     public function insert($data){
         try
         {
             $result = $this->db->insert('feedback', $data);
             $id = $this->db->insert_id();
             $q = $this->db->get_where('feedback', array('fb_id' => $id));
             if($q>0){
                 return $q->row();
             }else{
                 return FALSE;
             }
         }
         catch(MyException $e)
         {
             throw $e;
         }
     }

     public function getFeedback(){
         try
         {
           $this->db->select('*');
             $query = $this->db->get('feedback');
             $result = $query->result_array();
                $count = count($result);
                if(empty($count)){
                    return false;
                }
                else{
                    return $result;
                }
         }
         catch(MyException $e)
         {
             throw $e;
         }
     }
 }