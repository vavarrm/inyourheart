<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class AdminOdds extends CI_Controller {
	
	private $request = array();
	
	public function __construct() 
	{
		parent::__construct();	
		$this->load->model('TexasholdemInsuranceOdds_Model', 'odds');
		$this->load->model('AdminUser', 'admin_user');
		$this->response_code = $this->language->load('admin_response');
		$this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
		$this->get = $this->input->get();
		$this->post = $this->input->post();
		$this->pe_id = (!empty($this->get['pe_id']))?$this->get['pe_id']:'';
		
		$gitignore =array(

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
		
		$this->admin = $this->myfunc->getAdminUser($this->get['sess']);
    }
	
	public function doEdit()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='赔率设定';
		$output['message'] ='设定完成';
		$back =-1;
		try 
		{
			if(
				empty($this->post['odds'])
			)
			{
				$array = array(
					'status'	=>'002'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$data = $this->odds->batchUpload($this->post['odds']);
			
		}catch(MyException $e)
		{
			$back++;
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['message'] = $parames['message']; 
			$output['status'] = $parames['status']; 
			$this->myLog->error_log($parames);
		}
		
		$this->myfunc->back($back,$output['message']);
	}
	
	public function getOddsList()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='Odds List';
		try 
		{
			$ary =array(
				'control' =>__CLASS__,
				'func' =>'setting',
			);
			$row = $this->admin_user->getIdByCAndF($ary);
			$orderList = $this->odds->getOddsList(array('group_id'=>1));
			$output['body']['row']['info']['odds_list'] = $orderList;
			$output['body']['row']['form']['pe_id'] =$row['pe_id'];
			$output['body']['row']['form']['action'] = '/Api/'.__CLASS__."/doEdit";
		}
		catch(MyException $e)
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
