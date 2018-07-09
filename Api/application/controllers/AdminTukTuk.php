<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class AdminTukTuk extends CI_Controller {
	
	private $request = array();
	
	public function __construct() 
	{
		parent::__construct();	
		$this->load->model('TukTuk_Model', 'tuktuk');
		$this->load->library('session');
		$this->response_code = $this->language->load('admin_response');
		$this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
		$this->get = $this->input->get();
		$this->post = $this->input->post();
		
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
    }
	
	public function doEdit()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['status'] = '200';
		$output['title'] ='qr generator';
		$output['message'] ='generator ok';
		$back =-2;
		try 
		{
			if(
				empty($this->post['name']) ||
				empty($this->post['phone'])
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
				'name'	=>$this->post['name'],
                'phone'	=>$this->post['phone'],
                'password'	=>$this->post['pwd'],

			);

			$data  =  $this->tuktuk->insert($ary );

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
		$this->myfunc->back($back,$output['message']);
	
	}
	
	public function addForm()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='add Form';
		
		try 
		{
			$output['body']['row']['info'] = $data['row'];
			$output['body']['row']['form'] = array(
				'action'	=> '/Api/'.__CLASS__.'/doEdit',
				'pe_id'		=>$this->get['pe_id']
			);
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
	
	public function delQr()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='Qr  Del';
		try 
		{
	
			$id= (isset($this->request['id']))?$this->request['id']:'';
			if($id=="")
			{
				$array = array(
					'status'	=>'002'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$data = $this->tuktuk->del($id);
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
	
	public function getList($ary=array())
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='Restaurant List';
		try 
		{
			$ary['limit'] = (isset($this->request['limit']))?$this->request['limit']:10;
			$ary['p'] = (isset($this->request['p']))?$this->request['p']:1;
			$form['inputSearchControl'] = array(
				
			);
			if(!empty($form['inputSearchControl']))
			{
				foreach($form['inputSearchControl'] as $key => $value)
				{
					$$key= (isset($this->request[$key]))?$this->request[$key]:'';
				}
			}
			
			$form['selectSearchControl'] = array(

			);
			if(!empty($form['selectSearchControl']))
			{
				foreach($form['selectSearchControl'] as $key => $value)
				{
					$$key= (isset($this->request[$key]))?$this->request[$key]:'';
				}
			}
			$ary['order'] = (empty($this->request['order']))?array("t.id"=>'DESC'):$this->request['order'];
		    
			// $form['datetimeSearchControl'] = true;

			$form['table_add'] = __CLASS__."/add/".__CLASS__.'Add/';
			$form['table_del'] = "delQr";
			// $form['table_edit'] =  __CLASS__."/editQr/".__CLASS__.'editQr/';
			$temp=array(
				'pe_id' =>$this->pe_id,
				'ad_id' =>$this->admin['ad_id'],
			);
            $action_list = $this->admin_user->getAdminListAction($temp);

            $datetime_start = (isset($this->request['datetime_start']))?$this->request['datetime_start']:'';
            $datetime_end = (isset($this->request['datetime_end']))?$this->request['datetime_end']:'';
            if($datetime_start !="")
            {
                $datetime_start = date('Y-m-d H:i' ,strtotime($datetime_start));
            }

            if($datetime_end !="")
            {
                $datetime_end = date('Y-m-d H:i' ,strtotime($datetime_end));
            }

            $ary['datetime_start'] = array(
                'value'	=>$datetime_start,
                'operator'	=>'>=',
            );
            $ary['datetime_end'] = array(
                'value'	=>$datetime_end,
                'operator'	=>'<=',
            );
			$ary['fields'] = array(
				'id'				=>array('field'=>'t.id AS id','AS' =>'id'),
				'phone'				=>array('field'=>'t.phone AS phone','AS' =>'phone'),
				'name'		        =>array('field'=>'t.name AS name ','AS' =>'name'),

			);
			$list = $this->tuktuk->getList($ary);
			$output['body'] = $list;
			$output['body']['fields'] = $ary['fields'] ;
			$output['body']['subtotal_fields'] = $ary['subtotal'] ;
			$output['body']['form'] =$form;
			$output['body']['action_list'] =$action_list;
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
