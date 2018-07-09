<?php
class Contact extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Contact_Model','contact');
    }
    public function index(){
        echo 'index';
    }
    public function doInsert(){
            $data=array(
                'con_name' =>$this->input->post('name'),
                'con_email'  =>$this->input->post('email'),
                'con_phone'=>$this->input->post('phone'),
                'con_message'=>$this->input->post('message'),
            );
            if($data){
                $result=$this->contact->insert($data);
                $d['json'] = $result;
                echo json_encode($d);
            }else{

            }
    }

}