<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class HdPokerInsurance extends CI_Controller {
	
	private $request = array();
	
	public function __construct() 
	{
		parent::__construct();	
		$this->load->model('TexasholdemInsuranceOrder_Model', 'order');
		$this->load->model('TexasholdemInsuranceOdds_Model', 'odds');
		$this->load->model('user_Model', 'user');
		$this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
		$this->get = $this->input->get();
		$this->post = $this->input->post();
		$this->response_code = $this->language->load('response');
		$gitignore =array(
			'login',
			'logout'
		);
			
		try 
		{
			$checkUser = $this->myfunc->checkUser($gitignore);
			if($checkUser !="200")
			{
				$array = array(
					'status'	=>'000'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}

			$encrypt_user_data  = $this->session->userdata('encrypt_user_data');
			$decrypt_user_data= $this->myfunc->decryptUser($this->get['sess'], $encrypt_user_data);
			$this->user_data = $decrypt_user_data;
			
			
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
	
	public function getOdds()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='getOdds';
		try 
		{
			$rows= $this->odds->getOddsList(array('group_id' =>1));
			if(!empty($rows))
			{
				foreach($rows as $row)
				{
					$output['body']['odds'][$row['odds_outs']] = $row['odds_value'];
				}
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
		}
		
		$this->myfunc->response($output);
	}
	
	public function chenkUserCode()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='chenk User Code';
		try 
		{
			$ucode = (isset($this->request['ucode']))?$this->request['ucode']:'';
			if($ucode=="")
			{
				$array = array(
					'status'	=>'000'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$ary = array(
				'u_code'	=>$ucode,
				'u_id'		=>$this->user_data['u_id'],
			);
			$check = $this->user->checkUserCode($ary);
			$output['body']['check']=$check ['total'];
			
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
	
	public function uploadResult()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='uploadResult';
		try 
		{
			$order_id = (isset($this->request['order_id']))?$this->request['order_id']:'';
			$order_number = (isset($this->request['order_number']))?$this->request['order_number']:'';
			$result = (isset($this->request['result']))?$this->request['result']:'';
			$payamount = (isset($this->request['payamount']))?$this->request['payamount']:0;
			$pot = (isset($this->request['pot']))?$this->request['pot']:0;
			$amount = (isset($this->request['amount']))?$this->request['amount']:0;
			$i_maximum = (isset($this->request['i_maximum']))?$this->request['i_maximum']:0;
			if(
				$order_id=="" ||
				($result !="pay"  && $result!="nopay")||
				$payamount >$pot  ||
				$amount >$i_maximum
			)
			{
				$array = array(
					'status'	=>'000'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			if($result !="pay")
			{
				$payamount=0;
			}
			
			$ary = array(
				'result'	=>$result,
				'order_id'	=>$order_id,
				'u_id'		=>$this->user_data['u_id'],
				'payamount'	=>$payamount,
				'order_number'	=>$order_number,
			);
			$check = $this->order->updataResult($ary);
			$output['body']['check']=$check ['total'];
			
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
	
	public function insert()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='insert order';
		try 
		{
			$players = (isset($this->request['players']))?intval($this->request['players']):0;
			$outs = (isset($this->request['outs']))?intval($this->request['outs']):0;
			$odds = (isset($this->request['odds']))?$this->request['odds']:0;
			$order_number = (isset($this->request['odds']))?$this->request['order_number']:'';
			$insuredamount = (isset($this->request['insuredamount']))?$this->request['insuredamount']:0;
			$pot = (isset($this->request['pot']))?$this->request['pot']:0;
			$amount = (isset($this->request['amount']))?$this->request['amount']:0;
			$i_maximum = (isset($this->request['i_maximum']))?$this->request['i_maximum']:0;
			$percentage50 = (isset($this->request['percentage50']))?$this->request['percentage50']:0;
			$payamount = (isset($this->request['payamount']))?$this->request['payamount']:0;
			$result = ($this->request['result']=="pay" || $this->request['result']=="nopay")?$this->request['result']:'';
			
			$round = (isset($this->request['round']))?$this->request['round']:'';
			$ucode = (isset($this->request['ucode']))?$this->request['ucode']:'';
			

			$ary = array(
				'u_code'	=>$ucode,
				'u_id'		=>$this->user_data['u_id'],
			);
			$check = $this->user->checkUserCode($ary);
			$_odds = $this->odds->getOddsByOuts($outs);
			if(
				$players <2 ||
				$players >4 ||
				$outs <1  ||  $outs>20 ||
				$odds ==0 ||
				$insuredamount ==0 ||
				$pot ==0 ||
				$amount ==0 ||
				$i_maximum ==0 ||
				$percentage50 ==0 ||
				($round !="flop" && $round!="turn") ||
				$amount >$i_maximum ||
				$insuredamount >$pot ||
				$_odds['odds_value'] !=$odds ||
				$result ==''||
				$payamount > $pot 
			){
				$array = array(
					'status'	=>'000'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
				
			$ary =array(
				'round'=>$round,
				'outs'=>$outs,
				'odds'=>$odds,
				'pot'=>$pot,
				'round'=>$round ,
				'maximun'=>$i_maximum,
				'maximun_p50'=>$percentage50,
				'amount'=>$amount,
				'u_id'=>$this->user_data['u_id'],
				'pay'=>$insuredamount,
				'players' =>$players,
				'order_number'	=>$order_number,
				'result'	=>$result,
				'payamount'	=>$payamount,
			);
			
			$output['body'] = $this->order->insert($ary);
			
			// if($players )
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
