<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Publish extends CI_Controller {
	
	private $request = array();
	
	public function __construct() 
	{
		
		parent::__construct();	
		$this->load->model('AdminUser_Model', 'admin_user');
		$this->load->model('UserDelivery_Model', 'delivery');
		$this->response_code = $this->language->load('admin_response');
		$this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
		$this->get = $this->input->get();
		$this->post = $this->input->post();
		
		$gitignore =array(
			// 'login',
		);
		
		try 
		{
			
			$checkAdmin = $this->myfunc->checkAdmin($gitignore);
			if($checkAdmin !="200")
			{
				$array = array(
					'status'	=>$checkAdmin
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			

		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['message'] = $parames['message']; 
			$output['status'] = $parames['status']; 
			$this->myLog->error_log($parames);
			$this->myfunc->response($output);
			exit;
		}
		
    }

	
	public function uploadUserDelivery()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='Publish';
		try 
		{
		
			$to_uid = "1";
			$push_api_url  ="http://".$_SERVER['HTTP_HOST'].":2121/";
			$post_data = array(
			   "type" => "publish",
			   "action"	=>"TukTukgo",
			   "to" => '1', 
			);
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
			curl_setopt ( $ch, CURLOPT_POST, 1 );
			curl_setopt ( $ch, CURLOPT_HEADER, 0 );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
			curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
			$return = curl_exec ( $ch );
			curl_close ( $ch );
			var_export($return);
			
		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['status'] = $parames['status']; 
			$output['message'] = $parames['message']; 
			$this->myLog->error_log($parames);
		}
		
		$this->myfunc->response($output);
	}
	

	
	
}
