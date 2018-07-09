<?php
	class User_Model extends CI_Model 
	{
		function __construct()
		{
			
			parent::__construct();
			$this->load->database();
			$query = $this->db->query("set time_zone = '+7:00'");
			$error = $this->db->error();
			if($error['message'] !="")
			{
				$MyException = new MyException();
				$array = array(
					'el_system_error' 	=>"set time_zone error" ,
					'status'	=>'000'
				);
				
				$MyException->setParams($array);
				throw $MyException;
			}
		}
		
		
		public function getUserTicketById($id)
		{
			$status ='000';
			try
			{
				$sql ="SELECT ticket FROM user WHERE id =?";
				$bind= array(
					$id
				);
				
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$row = $query->row_array();
				$query->free_result();
				return $row;
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function getUserByid($id)
		{
			$status ='000';
			try
			{
				$sql ="SELECT * FROM user WHERE id =?";
				$bind= array(
					$id
				);
				
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$row = $query->row_array();
				$query->free_result();
				return $row;
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function getUserByPhone($phone)
		{
			$status ='000';
			try
			{
				$sql ="SELECT * FROM user WHERE phone =?";
				$bind= array(
					$phone
				);
				
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$row = $query->row_array();
				$query->free_result();
				
				return $row;
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function  register($ary)
		{
			$status = '000';
			$row = array(
				'affected_rows'=>0
			);
			try
			{
				$this->db->trans_begin();
				if(empty($ary))
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				$sql ="	INSERT INTO user (last_name, first_name,phone, password)
						VALUES(?,?,?,?)";
				$bind =array(
					$ary['fname'],
					$ary['lname'],
					$ary['phone'],
					MD5($ary['password']),
				);
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					if($error['code'] =='1062')
					{
						$status ='002';
					}
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$affected_rows = $this->db->affected_rows();
				$id = $this->db->insert_id();
				$this->db->trans_commit();
				if($affected_rows >0)
				{
					$row['affected_rows'] = $affected_rows ;
					$row['user']  = $this->getUserByid($id);
				}
				return $row;
			}	
			catch(MyException $e)
			{
				// $this->db->trans_rollback();
				throw $e;
			}
			
		}
	}
?>