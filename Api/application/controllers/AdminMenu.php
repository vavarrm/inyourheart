<?php
/**
 * Created by PhpStorm.
 * User: Sihalive
 * Date: 5/15/2018
 * Time: 12:38 PM
 */

class AdminMenu extends CI_Controller
{
    private $request = array();
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_Model', 'menu');
        $this->load->model('Category_Model', 'category');
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
                'id'				 	  		=>array('field'=>'t.id AS id','AS' =>'id','hide'	=>true),
                'category'						=>array('field'=>"concat(ca.kh_name,' ',ca.en_name,' ',ca.zh_name) AS category",'AS' =>'category'),
                'full_name'						=>array('field'=>"concat(t.kh_name,' ',t.en_name,' ',t.zh_name) AS full_name",'AS' =>'full_name'),
				'unit_price'				 	  		=>array('field'=>"concat('USD ：  ', t.unit_price) AS unit_price",'AS' =>'unit_price'),
            );
			
			
		
			
            $list = $this->menu->getList($ary);
			
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

}