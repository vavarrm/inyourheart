<?php
/**
 * Created by PhpStorm.
 * User: Sihalive
 * Date: 5/15/2018
 * Time: 12:38 PM
 */

class AdminCustomer extends CI_Controller
{
    private $request = array();

    public function __construct()
    {
        parent::__construct();
		$this->load->model('Customer_Model', 'customer');
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


	public function doAddTicket()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['status'] = '200';
		$output['title'] ='AddTicket';
		$output['message'] ='AddTicket ok';
		$back =-2;
		try 
		{
			if(
				intval($this->post['id'])  <=0 ||
				intval($this->post['ticket']) <=0
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
				'id'	=>$this->post['id'],
                'ticket'	=>$this->post['ticket']

			);

			$data  =  $this->customer->addTicket($ary);

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
	
	public function addTiketForm()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='add Form';
		
		try 
		{
			$id= $this->request['id'];
			$row= $this->customer->getTicketNumberByid($id);
			$output['body']['row']['info'] = $row;
			$output['body']['row']['form'] = array(
				'action'	=> '/Api/'.__CLASS__.'/doAddTicket',
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
				'status'	=>array(
					array('value' =>'registered' ,'text'=>'registered'),
					array('value' =>'lock' ,'text'=>'lock'),
					array('value' =>'verify' ,'text'=>'verify'),
				)
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

            // $form['table_add'] = __CLASS__."/add/".__CLASS__.'Add/';
            // $form['table_del'] = "delQr";
            // $form['table_edit'] =  __CLASS__."/editQr/".__CLASS__.'editQr/';
			
			
            $temp=array(
                'pe_id' =>$this->get['pe_id'],
                'ad_id' =>$this->admin['ad_id'],
            );
            $action_list = $this->admin_user->getAdminListAction($temp);


            $ary['fields'] = array(
                'id'				    =>array('field'=>'t.id AS id','AS' =>'id'),
                'name'				    =>array('field'=>"CONCAT(t.last_name,'-',	t.first_name) AS name",'AS' =>'name'),
                'phone'				    =>array('field'=>"t.phone AS phone",'AS' =>'phone'),
                'ticket'				    =>array('field'=>"t.ticket AS ticket",'AS' =>'ticket'),
                'status'				    =>array('field'=>"t.status AS status",'AS' =>'status'),
            );
			
			$ary['t.status'] = array(
				'value' =>$status,
				'logic' =>'AND',
				'operator' =>'=',
			);
			
            $list = $this->customer->getList($ary);
			
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