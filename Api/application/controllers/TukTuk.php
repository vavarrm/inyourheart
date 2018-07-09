<?php
class TukTuk extends CI_Controller{
   	private $request = array();
	
	public function __construct() 
	{
		parent::__construct();	
		
		$this->load->model('TukTuk_Model', 'tuktuk');
		$this->load->model('UserDelivery_Model', 'delivery');
		
		$this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
		$this->get = $this->input->get();
		$this->post = $this->input->post();
		$this->response_code = $this->language->load('response');
		 
		$this->tuktuk_sess = $this->session->userdata('tuktuk_sess');
		

		$gitignore =array(
			'login',
		);

		try 
		{
			
			$checkLogin  = $this->myfunc->checkUser($gitignore, $this->tuktuk_sess);
			if($checkLogin !="200")
			{
				$array = array(
					'status'	=>	$checkLogin 
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
		
			if(!empty($this->tuktuk_sess))
			{
				$output['body']['islogin']='1';
				$output['body']['tuktukid']=$this->tuktuk_sess['id'];
				$output['body']['order']=$this->delivery->getTukTukOrder($this->tuktuk_sess['id']);
				// var_dump($this->delivery->getTukTukOrder($this->tuktuk_sess['id']));
				
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
		$output['title'] ='司机登入';
		$islogin = 0;
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
			$phoneNumber = $this->myfunc->parsePhoneNumbrer($this->request['phone']);

			
			$ip = $this->myfunc->getIP();
			
			$tuktuk_driver = $this->tuktuk->getRowByPhone($phoneNumber);
			if(empty($tuktuk_driver))
			{
				$array = array(
					'status'	=>'015'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			if(md5($this->request['password']) != $tuktuk_driver['password'])
			{
				$array = array(
					'status'	=>'016'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$this->session->set_userdata('tuktuk_sess', $tuktuk_driver);
			$islogin = 1;
			$output['body'] = array(
				'islogin'	=>$islogin,
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