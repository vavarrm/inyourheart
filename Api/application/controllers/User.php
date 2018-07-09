<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller {
	
	private $request = array();
	
	public function __construct() 
	{
		parent::__construct();	
		
		$this->load->model('User_Model', 'user');
		$this->load->model('UserDelivery_Model', 'user_delivery');
		$this->load->model('UserVerifyCode_Model', 'verify_code');
		
		$this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
		$this->get = $this->input->get();
		$this->post = $this->input->post();
		$this->response_code = $this->language->load('response');
		 
		$this->user_sess = $this->session->userdata('user_sess');
		
		$gitignore =array(
			'login',
		);

		try 
		{
			
			$checkUser = $this->myfunc->checkUser($gitignore, $this->user_sess );
			
			if($checkUser !="200")
			{
				$array = array(
					'status'	=>$checkUser
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			

		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['message'] = $parames['message']; 
			$output['status'] = $parames['status']; 
			$this->myLog->error_log($parames);
			$this->myfunc->response($output);
			exit;
		}
		
    }

	
	public function getUser()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='取得登入資料';
		try 
		{
			$row =$this->user->getUserById($this->user_sess['id']);
			if(!empty($this->user_sess) && !empty($row))
			{
				$output['body']['islogin']='1';
				$user_delivery_row = $this->user_delivery->getDeliveryByNew(array('id'=>$this->user_sess['id']));
				$output['body']['user_delivery'] = $user_delivery_row;
				$output['body']['uid'] =$this->user_sess['id'];
				
				$output['body']['user_status'] = $row['status'];
				$output['body']['user_phone'] = $row['phone'];
				$output['body']['user_ticket'] = $row['ticket'];
				$output['body']['verifycode'] = $this->verify_code->getNoUsedVerifyCodeByUserId($this->user_sess['id']);
				$output['body']['token'] = $this->myfunc->getUserSess($this->user_sess);
			}else
			{
				$output['body']['islogin']='0';
			}
			
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
	
	public function login()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='登入';
		try 
		{
			if(
				empty($this->request['phone']) ||
				empty($this->request['password']) 
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$phoneNumberOne = substr($this->request['phone'] ,0,1 );
			if($phoneNumberOne==0)
			{
				$this->request['phone'] = substr($this->request['phone'] ,1,8 );
			}
			$this->request['phone'] = "855".$this->request['phone'];
			
			$ip = $this->myfunc->getIP();
			
			$user = $this->user-> getUserByPhone($this->request['phone']);
			if(empty($user))
			{
				$array = array(
					'status'	=>'015'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			if(md5($this->request['password']) != $user['password'])
			{
				$array = array(
					'status'	=>'016'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$this->session->set_userdata('user_sess', $user);
			$output['body'] = array(
				'islogin'	=>'1'
			);
			$output['message'] =  $this->response_code['201']; 
			
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
