<?php
/**
 * Created by PhpStorm.
 * User: Sihalive
 * Date: 5/15/2018
 * Time: 12:38 PM
 */

class AdminSales extends CI_Controller
{
    private $request = array();
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Order_Model', 'order');
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
			
			$itemList = $this->category->getOptionList();
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
			$row = $this->menu->getRowByID($id);
			$itemList = $this->category->getOptionList();
			$output['body']['row']['form'] = array(
				'action'	=> '/Api/'.__CLASS__.'/doEdit',
				'pe_id'		=>$this->get['pe_id']
			);
			$output['body']['row']['info'] = $row;
			$output['body']['row']['info']['itemList'] = $itemList;
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
        $output['title'] ='Add Menu ';
        $output['message'] ='Add Menu Ok';
        $back =-2;
        try
        {
			// $output['message'] = 'Add ok';
           
			$zh_name= (isset($this->post['zh_name']))?$this->post['zh_name']:'';
			$en_name= (isset($this->post['en_name']))?$this->post['en_name']:'';
			$kh_name= (isset($this->post['kh_name']))?$this->post['kh_name']:'';
			$unit_price= (isset($this->post['unit_price']))?$this->post['unit_price']:'';
			$category= (isset($this->post['category']))?$this->post['category']:'';
			
			if(($zh_name=="" && $en_name=="" && $kh_name=="" ) || $unit_price =="" || $category =="")
			{
				$array = array(
					'status'	=>'002'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$this->menu->add($this->post);
		   
        }catch(MyException $e)
        {
            $parames = $e->getParams();
            $parames['class'] = __CLASS__;
            $parames['function'] = __function__;
            $parames['message'] =  $this->response_code[$parames['status']];
			echo "D";
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
        $output['title'] ='Edit Menu ';
        $output['message'] ='Edit Menu  OK';
        $back =-2;
        try
        {
           
			$zh_name= (isset($this->post['zh_name']))?$this->post['zh_name']:'';
			$en_name= (isset($this->post['en_name']))?$this->post['en_name']:'';
			$kh_name= (isset($this->post['kh_name']))?$this->post['kh_name']:'';
			$id= (isset($this->post['id']))?$this->post['id']:'';
			$unit_price= (isset($this->post['unit_price']))?$this->post['unit_price']:'';
			$category= (isset($this->post['category']))?$this->post['category']:'';
			if(($zh_name=="" && $en_name=="" && $kh_name=="" ) || $unit_price =="" || $id="" || $category=="")
			{
				$array = array(
					'status'	=>'002'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$row = $this->menu->edit($this->post);
			
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
			$data = $this->menu->del($id);
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
				'status' =>array(
					array('value' =>'making' ,'text'=>'making' ,'selected'=>true),
					array('value' =>'checkout' ,'text'=>'checkout'),
					array('value' =>'badDebts' ,'text'=>'badDebts'),
				)
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
			$datetime_start = (isset($this->request['datetime_start']))?$this->request['datetime_start']:'';
			$datetime_end = (isset($this->request['datetime_end']))?$this->request['datetime_end']:'';
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
                'datetime'				 	  	=>array('field'=>'t.add_datetime AS datetime','AS' =>'datetime'),
                'status'				 	  	=>array('field'=>'t.status AS status','AS' =>'status'),
                'code'				 	  		=>array('field'=>'t.code AS code','AS' =>'code'),
				'original_total'				=>array('field'=>'t.total AS original_total','AS' =>'original_total'),
                'discount'				 	  	=>array('field'=>'t.total AS discount','AS' =>'discount'),
				'total'				 	  		=>array('field'=>'t.total AS total','AS' =>'total'),
				'total'				 	  		=>array('field'=>'t.total AS total','AS' =>'total'),
            );
			
			
			if($status=="all")
			{
				$status="";
				// $status="checkout";
			}
			$ary['t.status'] = array(
				'value'=>$status,
				'operator'	=>'=',
				'logic'		=>'AND',
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

			$ary['subtotal'] =array(
				'SUM(t.total)' =>array("field"=>'subtotal')
			);
            $list = $this->order->getList($ary);
			
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
            $output['body']['total_info'] =$list['pageinfo']['subtotal_datalist'];
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

}