<?php
/**
 * Created by PhpStorm.
 * User: Sihalive
 * Date: 5/15/2018
 * Time: 12:38 PM
 */

class AdminPurchase extends CI_Controller
{
    private $request = array();
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Purchase_Model', 'purchase');
        $this->load->model('Account_Model', 'account');
        $this->load->model('Material_Model', 'material');
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
			$temp = $this->material->getItemList();
			foreach($temp as $value)
			{
				$itemList[$value['id']] =  $value;
			}
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


	
	
    public function doAdd()
    {
        $output['body']=array();
        $output['status'] = '200';
        $output['title'] ='Add Purchase';
        $output['message'] ='Add Purchase';
        $back =-2;
        try
        {
			$output['message'] = 'Add ok';
            $row = $this->purchase->add($this->post,$this->admin);
			if( $row ['affected_rows'] == 0)
			{
				$output['message'] = 'pleas input data';
				$back = -1;
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
	
			$code= (isset($this->request['id']))?$this->request['id']:'';
			if($code==="")
			{
				$array = array(
					'status'	=>'002'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$data = $this->purchase->delBycode($code);
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
            $ary['order'] = (empty($this->request['order']))?array("t.code"=>'DESC'):$this->request['order'];

            // $form['datetimeSearchControl'] = true;
            $form['dateSearchControl'] = true;
			$datetime_start = (isset($this->request['datetime_start']))?$this->request['datetime_start']:'';
			$datetime_end = (isset($this->request['datetime_end']))?$this->request['datetime_end']:'';
			if($datetime_start !="")
			{
				$datetime_start = date('Y-m-d' ,strtotime($datetime_start));
			}
			
			if($datetime_end !="")
			{
				$datetime_end = date('Y-m-d' ,strtotime($datetime_end));
			}
			
			if($datetime_start =="" && $datetime_end  =="")
			{
				$datetime_start = date('Y-m-d' ,time());
				$datetime_end = date('Y-m-d' ,time());
			}
			
			$ary['datetime_start'] = array(
				'value'	=>$datetime_start,
				'format'	=>'%Y-%m-%d',
				'operator'	=>'>=',
			);
			$ary['datetime_end'] = array(
				'value'	=>$datetime_end,
				'format'	=>'%Y-%m-%d',
				'operator'	=>'<=',
			);
			
            $form['table_add'] = __CLASS__."/add/".__CLASS__.'Add/';
            $form['table_del'] = "del";
            // $form['table_edit'] =  __CLASS__."/editQr/".__CLASS__.'editQr/';
			
			
			
            $temp=array(
                'pe_id' =>$this->get['pe_id'],
                'ad_id' =>$this->admin['ad_id'],
            );
            $action_list = $this->admin_user->getAdminListAction($temp);


			
            $ary['fields'] = array(
                'id'				    	=>array('field'=>'t.code AS id','AS' =>'id','hide'=>true),
                'code'				    	=>array('field'=>'t.code AS code','AS' =>'code'),
                'khr'				    	=>array('field'=>'t.khr AS khr','AS' =>'khr'),
                'usd'				   		=>array('field'=>'t.usd AS usd','AS' =>'usd'),
                'date'				   		=>array('field'=>"DATE_FORMAT(add_datetime,'%Y-%m-%d' ) AS date",'AS' =>'date'),
                'subtotal'				    =>array('field'=>sprintf(" CONCAT('$',ROUND((t.usd + (t.khr/%1\$d)),2),'  ,  KHR  ', ROUND((t.khr + (t.usd*%1\$d)))) AS subtotal", $this->config->item('khrtousd')),'AS' =>'subtotal'),
            );
			
			
			$ary['t.type'] = array(
				'value' =>'purchase',
				'logic' =>'AND',
				'operator' =>'=',
			);
			
			$ary['t.is_del'] = array(
				'value' =>'false',
				'logic' =>'AND',
				'operator' =>'=',
			);
			
            $list = $this->account->getList($ary);
			
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