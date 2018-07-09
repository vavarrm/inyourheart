<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron extends CI_Controller {
	
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('TexasholdemInsuranceOrder_Model', 'order');
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		   $myip = $_SERVER['HTTP_CLIENT_IP'];
		}else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		   $myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
		   $myip= $_SERVER['REMOTE_ADDR'];
		}
		if($myip!="13.229.126.143" && $myip !="127.0.0.1")
		{
			die('ddd');
		}
    }
	
	public function subtotal(){
       
		$row= $this->order->subtotalLastDay();
		if(empty($row))
		{
			$smstex="Please Check Out";
		}else
		{
			$smstex =sprintf("Insurance Income %s to %s  Total %s",$row['startdatetime'], $row['enddatetime'] , $row['income']);
		}
		$gsm="85516995372;85512321402;85517684220;85511923080";
		$url="http://client.mekongsms.com/api/postsms.aspx";
		$post = array(
			'username'	=>'tsai_sms@smartmkn',
			'pass'		=>md5('3xxkdkj@c'),
			'cd'		=>'Cust001',
			'sender'	=>"Sihalive",
			'smstext'	=>$smstex,
			'isflash'	=>0,
			'gsm'		=>$gsm,
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post)); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
		$output = curl_exec($ch); 
		curl_close($ch);
    }
}
