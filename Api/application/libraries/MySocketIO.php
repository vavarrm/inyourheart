<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MySocketIO
{
	private $CI ;
	public function __construct() 
	{
		$this->CI =& get_instance();
		try 
		{
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
	
	public function push($ary)
	{
		$default = array(
			'type'	=>'publish'
		);
		
		$get = array_merge($default, $ary);
		
		if(!empty($get))
		{
			foreach($get as $key=> $value)
			{
				$get_str.=sprintf("%s=%s&", $key, $value);
			}
		}
		
		$url = $_SERVER['SERVER_NAME'].":2121?".$get_str;
 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		return  $output;
	}
}