<?php
/**
 * Created by PhpStorm.
 * User: Sihalive
 * Date: 5/15/2018
 * Time: 12:38 PM
 */

class AdminAccount extends CI_Controller
{
    private $request = array();
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Account_Model', 'account');
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
			$this->admin = $this->myfunc->getAdminUser( $this->get['sess']);
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
	
	
	public function cashFlow()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='cash Flow';
		
		try 
		{
			$data = $this->account->getcashFlow();
			$output['body']['row']['info'] = $data;
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
	
	public function addFormPage()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='add Form';
		
		try 
		{
			$output['body']['row']['form'] = array(
				'action'	=> '/Api/'.__CLASS__.'/doAdd',
				'pe_id'		=>$this->get['pe_id']
			);
			$output['body']['row']['info'] = array(
				'itemList'	=> $itemList
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

	public function editFormPage()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='edit Form';
		
		try 
		{
			$id= (isset($this->request['id']))?$this->request['id']:'';
			if($id =="")
			{
				$array = array(
					'status'	=>'002'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			// var_dump($this->request);
			$row = $this->material->getRowByID($id);
			$output['body']['row']['form'] = array(
				'action'	=> '/Api/'.__CLASS__.'/doEdit',
				'pe_id'		=>$this->get['pe_id']
			);
			$output['body']['row']['info'] = $row;
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
	
    public function doAdd()
    {
        $output['body']=array();
        $output['status'] = '200';
        $output['title'] ='Add Material ';
        $output['message'] ='Add Material Ok';
        $back =-2;
        try
        {
			// $output['message'] = 'Add ok';
           
			$zh_name= (isset($this->post['zh_name']))?$this->post['zh_name']:'';
			$en_name= (isset($this->post['en_name']))?$this->post['en_name']:'';
			$kh_name= (isset($this->post['kh_name']))?$this->post['kh_name']:'';
			$unit= (isset($this->post['unit']))?$this->post['unit']:'';
			if(($zh_name=="" && $en_name=="" && $kh_name=="" ) ||$unit =="")
			{
				$array = array(
					'status'	=>'002'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$this->material->add($this->post);
		   
        }catch(MyException $e)
        {
            $parames = $e->getParams();
            $parames['class'] = __CLASS__;
            $parames['function'] = __function__;
            $parames['message'] =  $this->response_code[$parames['status']];
            $output['message'] = $parames['message'];
            $output['status'] = $parames['status'];
            $this->myLog->error_log($parames);
			$back =-1;
			
        }
        $this->myfunc->back($back,$output['message']);

    }
	
	public function doEdit()
    {
        $output['body']=array();
        $output['status'] = '200';
        $output['title'] ='Edit Material ';
        $output['message'] ='Edit Material  OK';
        $back =-2;
        try
        {
           
			$zh_name= (isset($this->post['zh_name']))?$this->post['zh_name']:'';
			$en_name= (isset($this->post['en_name']))?$this->post['en_name']:'';
			$kh_name= (isset($this->post['kh_name']))?$this->post['kh_name']:'';
			$id= (isset($this->post['id']))?$this->post['id']:'';
			$unit= (isset($this->post['unit']))?$this->post['unit']:'';
			if(($zh_name=="" && $en_name=="" && $kh_name=="" ) || $unit =="" || $id="")
			{
				$array = array(
					'status'	=>'002'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$row = $this->material->edit($this->post);
			
			if($row ['affected_rows'] == 0)
			{
				$array = array(
					'status'	=>'004'
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
			$back =-1;
			
        }
        $this->myfunc->back($back,$output['message']);

    }

	public function del()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='del';
		$output['message'] = 'del OK';
		try 
		{
	
			$id = (isset($this->request['id']))?$this->request['id']:'';
			if($id ==="")
			{
				$array = array(
					'status'	=>'002'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$data = $this->material->del($id);
			if($data['affected_rows'] ==0)
			{
				$array = array(
					'status'	=>'003'
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
		}
		$this->myfunc->response($output);
	}
	
    public function getList($ary=array())
    {
        $output['body']=array();
        $output['status'] = '200';
        $output['title'] ='Purchase List';
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
				// 'status'	=>array(
					// array('value' =>'processing' ,'text'=>'processing'),
					// array('value' =>'tuktukgo' ,'text'=>'tuktukgo'),
					// array('value' =>'complete' ,'text'=>'complete'),
					// array('value' =>'cancel' ,'text'=>'cancel'),
					// array('value' =>'tuktukarrival' ,'text'=>'tuktukarrival'),
					// array('value' =>'tuktukback' ,'text'=>'tuktukback'),
				// )
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
            // $form['dateSearchControl'] = true;
			
			
            $form['table_add'] = __CLASS__."/add/".__CLASS__.'Add/';
            $form['table_del'] = "del";
            $form['table_edit'] =  __CLASS__."/edit/".__CLASS__.'editForm/';
			
			
			
            $temp=array(
                'pe_id' =>$this->get['pe_id'],
                'ad_id' =>$this->admin['ad_id'],
            );
            $action_list = $this->admin_user->getAdminListAction($temp);


			
            $ary['fields'] = array(
                'id'				   =>array('field'=>'t.id AS id','AS' =>'id'),
                'full_name'				=>array('field'=>"concat(t.kh_name,' ',t.en_name,' ',t.zh_name) AS full_name",'AS' =>'full_name'),
				'unit'				    		=>array('field'=>'t.unit AS unit','AS' =>'unit'),
            );
			
			
		
			
            $list = $this->material->getList($ary);
			
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
	
	public function getDetails()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='Get Details';
		
		try 
		{
			$code =  $this->request['id'];
			$rows = $this->purchase->getDetailsByCode($code);
			$row = $this->account->getDetailsByCode($code);
			$output['body']['row']['info']['details'] = $rows ;
			$output['body']['row']['info']['info'] = $row;
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

	
	public function dateReport($ary=array())
    {
        $output['body']=array();
        $output['status'] = '200';
        $output['title'] ='Purchase List';
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
				// 'status' =>array(
					// array('value' =>'making' ,'text'=>'making' ,'selected'=>true),
					// array('value' =>'checkout' ,'text'=>'checkout'),
					// array('value' =>'badDebts' ,'text'=>'badDebts'),
				// )
			);
			
            if(!empty($form['selectSearchControl']))
            {
                foreach($form['selectSearchControl'] as $key => $value)
                {
                    $$key= (isset($this->request[$key]))?$this->request[$key]:'';
                }
            }
            $ary['order'] = (empty($this->request['order']))?array("t.code"=>'DESC'):$this->request['order'];

            // $form['datetimeSearchControl'] = true;
            $form['dateSearchControl'] = true;
			$datetime_start = (isset($this->request['datetime_start']))?$this->request['datetime_start']:date('Y-m-d' ,time());
			$datetime_end = (isset($this->request['datetime_end']))?$this->request['datetime_end']:date('Y-m-d' ,time());
			// var_dump($this->request);
			if($datetime_start !="")
			{
				$datetime_start = date('Y-m-d' ,strtotime($datetime_start));
			}
			
			if($datetime_end !="")
			{
				$datetime_end = date('Y-m-d' ,strtotime($datetime_end));
			}
			
            // $form['table_add'] = __CLASS__."/add/".__CLASS__.'Add/';
            // $form['table_del'] = "del";
            // $form['table_edit'] =  __CLASS__."/edit/".__CLASS__.'editForm/';
			
			
			
            $temp=array(
                'pe_id' =>$this->get['pe_id'],
                'ad_id' =>$this->admin['ad_id'],
            );
            $action_list = $this->admin_user->getAdminListAction($temp);


			
            $ary['fields'] = array(
                'id'				 	  		=>array('field'=>'t.code AS id','AS' =>'id','hide'	=>true),
                'datetime'				 	  	=>array('field'=>'DATE_FORMAT(t.add_datetime,"%Y-%m-%d") AS datetime','AS' =>'datetime'),
                'type'				 	  		=>array('field'=>'t.type AS type','AS' =>'type'),
				'usd_amount'					=>array('field'=>sprintf("ROUND(SUM(t.khr/%1\$d+t.usd),2) AS usd_amount",$this->config->item('khrtousd')),'AS' =>'usd_amount'),
                'khr_amount'					=>array('field'=>sprintf("ROUND(SUM(t.khr+(t.usd*%1\$d)),0) AS khr_amount",$this->config->item('khrtousd')),'AS' =>'khr_amount'),
				// 'original_total'				=>array('field'=>'t.total AS original_total','AS' =>'original_total'),
                // 'discount'				 	  	=>array('field'=>'t.total AS discount','AS' =>'discount'),
				// 'total'				 	  		=>array('field'=>'t.total AS total','AS' =>'total'),
				// 'total'				 	  		=>array('field'=>'t.total AS total','AS' =>'total'),
            );
			
			$ary['t.type'] = array(
				'value'=>array('sale','purchase'),
				'operator'	=>'=',
				'logic'		=>'AND',
			);
			
			$ary['t.is_del'] = array(
				'value'=>'false',
				'operator'	=>'=',
				'logic'		=>'AND',
			);
		
			$ary['groupby'] = array(
				'DATE(t.add_datetime)',
				't.type'
			);
			$ary['datetime_start'] = array(
				'value'	=>$datetime_start,
				'operator'	=>'>=',
				'format'	=>'%Y-%m-%d'
			);
			$ary['datetime_end'] = array(
				'value'	=>$datetime_end,
				'operator'	=>'<=',
				'format'	=>'%Y-%m-%d'
			);
	
			
			

            $list = $this->account->dateReport($ary);
			
            $output['body'] = $list;
            $output['body']['fields'] = $ary['fields'] ;
			if(!empty($ary['fields']))
			{
				foreach($ary['fields'] as $value)
				{
					if($value['hide'] != true)
					{
						$colspan++;
					}
				}
			}
            // $output['body']['total_info'] =$list['pageinfo']['subtotal_datalist'];
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