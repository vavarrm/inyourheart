<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class PosApi extends CI_Controller {
	
	private $request = array();
	
	public function __construct() 
	{
		parent::__construct();	
		
		$this->load->model('User_Model', 'user');
		$this->load->model('UserVerifyCode_Model', 'userVC');
		$this->load->model('UserDelivery_Model', 'delivery');
		$this->load->model('Qrcode_Model', 'qrcode');
		
		$this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
		$this->get = $this->input->get();
		$this->post = $this->input->post();
		$this->response_code = $this->language->load('response');
		 
		$this->user_sess = $this->session->userdata('user_sess');
		
		$gitignore =array(
			'login',
			'logout',
			'registerQr',
			'registerQrForm'
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

	
	
	public function calltuktuk()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='';
		try 
		{
			
			if(
				$this->request['id'] ==''
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$ary = array(
				'qrcode_id' =>$this->request['id'] ,
				'user_id' 	=>$this->user_sess['id'],
				'max'		=>1,
				'message'	=>$this->request['message']
			);
			$row = $this->delivery->insert($ary);
			if($row['affected_rows'] == 0)
			{
				$array = array(
					'status'	=>'012'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$output['message'] = $this->response_code['202']; 
			$ary =array(
				'action'	=>'callAdminCallTukTuk',
				'to'		=>'system',
				'content'	=>''
			);
			$output['body']['socketPush'] = $this->socketIO->push($ary);
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
	
	public function resendVerifyCode()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='';
		// $output['']
		try 
		{
			
			$row = $this->user_sess;
			$row['ip'] =  $this->myfunc->getIP();
			$VerifyCode  = $this->userVC->getVerifyCode($row);
			$row['VerifyCode'] = $VerifyCode['code'];
			$code = $this->userVC->insert($row);
			
			// $ary = array(
				// 'smstext' 	=>sprintf('This is your VerifyCode',$row['VerifyCode']),
				// 'gsm'		=>$this->user_sess['phone']
			// );
			// $sendSms = $this->myfunc->sendSms($ary);
			// $sendSms.="[Success]";
			// echo $this->user_sess['phone'];
			// echo $sendSms;
				$smstex =  sprintf('This is your VerifyCode %s',$row['VerifyCode']);
				$gsm=$this->user_sess['phone'];
				$url=$_SERVER['SMS_REQUEST_URL'];
				$post = array(
					'username'	=>$_SERVER['SMS_USERNAME'],
					'pass'		=>md5($_SERVER['SMS_PASSWD']),
					'cd'		=>'Cust001',
					'sender'	=>"Sihalive",
					'smstext'	=>$smstex,
					'isflash'	=>0,
					'gsm'		=>$gsm,
				);
				$ch = curl_init();
				@curl_setopt($ch, CURLOPT_URL, $url);
				@curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
				@curl_setopt($ch, CURLOPT_POST, true);
				@curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post)); 
				@curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
				$sendSms = curl_exec($ch); 
				@curl_close($ch);
			
			if(!strpos($sendSms,"0[Success]"))
			{
				$array = array(
					'status'	=>'005'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$output['body'] = $code ;
			$output['message'] =  $this->response_code['203']; 
			// echo $this->response_code['203'];

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
	
	public function verifyCode()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='';
		try 
		{
			if(
				$this->request['vid'] =='' ||
				$this->request['verify_code'] =='' 
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$this->request['id'] =$this->user_sess['id'];
			$verifyCode = $this->userVC->checkVerifyCode($this->request);
			$output['message'] =  $this->response_code['202']; 
			
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
	
	public function registerQrForm()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='';
		try 
		{
		
			if(
				$this->request['id'] ==''
			)
			{
				$array = array(
					'status'	=>'011'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$row = $this->qrcode->getRow($this->request['id']);
			if(empty($row))
			{
				$array = array(
					'status'	=>'011'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$output['body'] = $row;
			$row = $this->delivery->getRowByUserIDAndEnd($this->user_sess['id']);
			if(empty($row) && !empty($this->user_sess['id']))
			{
				$output['body']['isuse'] = '0';
			}else
			{
				$output['body']['isuse'] = '1';
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
	
	public function registerQr()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='註冊';
		try 
		{
			if(empty($this->request))
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
		
			if(
				empty($this->request['fname']) ||
				empty($this->request['lname']) ||
				empty($this->request['phone']) ||
				empty($this->request['password']) ||
				empty($this->request['c_password']) 
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			
			if(!preg_match('/^[A-Za-z0-9]{8,12}$/', $this->request['password'])){
				$array = array(
					'status'	=>'010'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			if(
				!preg_match("/^[0-9]{8,9}$/", $this->request['phone'])
			)
			{
				$array = array(
					'status'	=>'013'
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
		
			$ary = array(
				'phone' =>$this->request['phone'],
				'ip'	=>$ip
			);
			$VerifyCode  = $this->userVC->getVerifyCode($ary);
			$row = $this->user->register($this->request);
			
			if($row['affected_rows'] <0)
			{
				$array = array(
					'status'	=>'003'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$row['user']['ip'] = $ip;
			
			$smstex =  sprintf('This is your VerifyCode %s',$VerifyCode['code']);
			$gsm=$this->user_sess['phone'];
			$url=$_SERVER['SMS_REQUEST_URL'];
			$post = array(
				'username'	=>$_SERVER['SMS_USERNAME'],
				'pass'		=>md5($_SERVER['SMS_PASSWD']),
				'cd'		=>'Cust001',
				'sender'	=>"Sihalive",
				'smstext'	=>$smstex,
				'isflash'	=>0,
				'gsm'		=>$gsm,
			);
			$ch = curl_init();
			@curl_setopt($ch, CURLOPT_URL, $url);
			@curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
			@curl_setopt($ch, CURLOPT_POST, true);
			@curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post)); 
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
			$sendSms = curl_exec($ch); 
			@curl_close($ch);
			
			
			if(!strpos($sendSms,"[Success]"))
			{
				$array = array(
					'status'	=>'005'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$row['user']['VerifyCode'] = $VerifyCode['code'] ;
			$code = $this->userVC->insert($row['user']);
			
			$output['body']['user'] = array(
				'id'			=>$row['user']['id'],
				'vid'			=>$code['id'],
			);
			$output['message'] =$this->response_code['201'];
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
