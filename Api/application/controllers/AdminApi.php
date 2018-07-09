<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class AdminApi extends CI_Controller {
	
	private $request = array();
	
	public function __construct() 
	{
		parent::__construct();	
		$this->load->model('AdminUser_Model', 'admin_user');
		$this->response_code = $this->language->load('admin_response');
		$this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
		$this->get = $this->input->get();
		$this->post = $this->input->post();
		
		$gitignore =array(
			'login',
			'logout'
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
	public function logout()
	{
		$this->session->unset_userdata('encrypt_admin_user_data');
		$this->myfunc->gotoUrl('/admin/login.html','logout ok');
	}
	
	public function index()
	{
	}
	
	private function doLogin($ary)
	{
		try 
		{
			$admin_user = $this->admin_user->getAdminByAccount($ary['account']);
			if(empty($admin_user))
			{
				$array = array(
					'status'	=>'005'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			if($admin_user['ad_status'] =='onlock')
			{
				$array = array(
					'status'	=>'006'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			if(md5($ary['password']) != $admin_user['ad_passwd'])
			{
				$array = array(
					'status'	=>'007'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			
			
			$randomKey = $this->token->getRandomKey();
			$rsaRandomKey = $this->token->publicEncrypt($randomKey);
			$data = array(
				'ad_id'  =>$admin_user['ad_id'],
				'account'  =>$admin_user['ad_account'],
				'ar_id'  =>$admin_user['ar_id'],
			);
			$encrypt_user_data = $this->token->AesEncrypt(serialize($data), $randomKey);
			$this->session->set_userdata('encrypt_admin_user_data', $encrypt_user_data);
			$urlRsaRandomKey = urlencode($rsaRandomKey) ;
			return $urlRsaRandomKey ;
		}catch(MyException $e)
		{
			throw $e; 
		}
	}
	
	public function getUser()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='get login info';
		try 
		{
		
			$urlRsaRandomKey =$this->get['sess'];
			$encrypt_data = $_SESSION['encrypt_admin_user_data'] ;
			$decrypt_data= $this->myfunc->decryptUser($urlRsaRandomKey, $encrypt_data);
			if(empty($decrypt_data))
			{
				$array = array(
					'status'	=>'006'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
	
			// $processingCount = $this->delivery->getCountByStatus('processing');
			$output['body']['admin_user'] = $decrypt_data;
			$output['body']['socket_push_data'] = array(
				'processingCount'	=>$processingCount
			);
			$data = $this->admin_user->getAdminMenuList($decrypt_data);
			$output['body']['menu_list'] =$data['list'];
		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['message'] = $parames['message']; 
			$output['status'] = $parames['status']; 
			$this->myLog->error_log($parames);
		}
		
		$this->myfunc->response($output);
	}
	
	
	
	public function login()
	{
		
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='login';
		try 
		{
			if( 
				$this->request['account'] =="" ||
				$this->request['password'] =="" 
			)
			{
				$array = array(
					'status'	=>'002'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$ary = array(
				'account'	=>$this->request['account'],
				'password'	=>$this->request['password'],
			);
			$sess = $this->doLogin($ary);
			$output['body']['sess']=$sess ;
			
		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['message'] = $parames['message']; 
			$output['status'] = $parames['status']; 
			$this->myLog->error_log($parames);
		}
		
		$this->myfunc->response($output);
	}
}
