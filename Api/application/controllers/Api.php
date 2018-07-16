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

	

	
	public function getMenu()
	{
		$output['body']=array();
		$output['status'] = '100';
		$output['title'] ='get Menu';
		try 
		{
			$data = $this->menu->getMenuIndexByCategory();
			$output['body']['data']['menu'] = $data['menu'];
			$output['body']['data']['category'] = $data['category'];
			
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
		$output['status'] = '100';
		$output['title'] ='add Order';
		try 
		{	
			
			$data = $this->order->add($this->request);
			$output['message']['body']['data'] = $data;
			
			
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
		try 
		{
			$data = $this->order->addMore($this->request);
			$output['message']['body']['data'] = $data;
			
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
