<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class PushSocket extends CI_Controller {
	
	
	public function __construct() 
	{
		parent::__construct();

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
			
			if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			   $myip = $_SERVER['HTTP_CLIENT_IP'];
			}else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			   $myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}else{
			   $myip= $_SERVER['REMOTE_ADDR'];
			}
			if($myip!="13.229.126.143" && $myip !="127.0.0.1")
			{
				$array = array(
					'status'	=>'017'
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

	public function  fixedQr()
	{
		echo $_SERVER['SERVER_NAME']; 
		$url = "http://localhost/path.php?get_var=test";
		 
		$ch = curl_init();
		 
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		 
		curl_close($ch);
		 
		echo $output;
	}
	
}
