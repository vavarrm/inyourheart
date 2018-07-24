<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Api extends CI_Controller {
	
	private $request = array();
	
	public function __construct() 
	{
		parent::__construct();	
		
		
		$this->load->model('Menu_Model', 'menu');
		$this->load->model('Order_Model', 'order');
		$this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
		$this->get = $this->input->get();
		$this->post = $this->input->post();
		$this->response_code = $this->language->load('response');
		 
		// $this->user_sess = $this->session->userdata('user_sess');
		
		// $gitignore =array(
			// 'login',
			// 'logout',
			// 'registerQr',
			// 'registerQrForm'
		// );

		try 
		{
			
			// $checkUser = $this->myfunc->checkUser($gitignore, $this->user_sess );
			
			// if($checkUser !="200")
			// {
				// $array = array(
					// 'status'	=>$checkUser
				// );
				// $MyException = new MyException();
				// $MyException->setParams($array);
				// throw $MyException;
			// }
			

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

	public function getBillForCode()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='get Bill';
		try 
		{
			if(
				$this->request['code'] =="" 
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$data = $this->order->getSaleDetailedByCode($this->request['code'],'making');
			if(empty($data))
			{
				$array = array(
					'status'	=>'008'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$output['body']['data'] = $data ;
			
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

	public function getNoCheckBillList()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='get Menu';
		try 
		{
		
			$data = $this->order->getListByStatusAndDelivery('making');
			$output['body']['data'] = $data ;
			
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
	
	public function getBillByForCheckBill()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='get Bill';
		try 
		{
			if(
				$this->request['code'] =="" 
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$khrtousd = $this->config->item('khrtousd');
			$data = $this->order->getBillForCheckBill($this->request['code']);
			$data['usdtoriel'] = $khrtousd;
			$output['body']['data'] = $data ;
			
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
	
	public function getMenu()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='get Menu';
		try 
		{
			for($i=1;$i<=20;$i++)
			{

				$noUseNumber[] = $i;
			}
			$data = $this->menu->getMenuIndexByCategory();
			$usingNumber = $this->order->getUsingNumber();
			$usingNumberAry = explode(',',$usingNumber['using_number']);
			$canUseNumber=array_diff($noUseNumber,$usingNumberAry);
			$khrtousd= $this->config->item('khrtousd');
			$output['body']['data']['menu'] = $data['menu'];
			$output['body']['data']['canUseNumber'] = $canUseNumber;
			$output['body']['data']['khrtousd'] = $khrtousd;
			$output['body']['data']['category'] = $data['category'];
			$output['body']['data']['usingNumber'] = $usingNumber;
			
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
	
	
	
	public function delMeals()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='del Meals';
		$output['message'] ='del ok';
		try 
		{	
			if(
				$this->request['code'] =="" ||
				count($this->request['meals']) ==0 
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			
			$data = $this->order->delMeals($this->request);
			$output['body']['data'] = $data;
			
			
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
	
	public function addOrder()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='add Order';
		$output['message'] ='add Order';
		try 
		{	
			if(
				$this->request['number'] =="" ||
				$this->request['delivery'] =="" ||
				$this->request['meals'] =="" 
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			
			$data = $this->order->add($this->request);
			$output['body']['data'] = $data;
			
			
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
	
	public function checkBill()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='checkBill';
		$output['message'] ='checkBill OK';
		try 
		{	
			if(
				$this->request['code'] =="" ||
				($this->request['pay_amount_usd'] ==0 && $this->request['pay_amount_riel'] == 0) ||
				($this->request['pay_amount_usd'] >0 && $this->request['pay_amount_riel'] > 0) ||
				($this->request['discount'] <0.8 || $this->request['discount'] >1)
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			
			$data = $this->order->checkBill($this->request);
			$output['body']['data'] = $data;
			
			
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
	
	public function addMoreOrder()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='add More By Order';
		$output['message'] ='add More OK';
		try 
		{
			if(
				$this->request['code'] =="" ||
				$this->request['meals'] =="" 
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$data = $this->order->addMore($this->request);
			$output['body']['data'] = $data;
			
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
