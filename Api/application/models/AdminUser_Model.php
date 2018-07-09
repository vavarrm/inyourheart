<?php
	class AdminUser_Model extends CI_Model 
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
		
		public function getIdByCAndF($ary)
		{
			try
			{
				if(empty($ary))
				{
					$MyException = new MyException();
					$array = array(
						'message' 	=>'ary empty',
						'status'	=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
					
				$bind = array(
					$ary['control'],
					$ary['func']
				);
				$sql ="SELECT  pe_id FROM permissions WHERE pe_control = ? AND pe_func=?";
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'000'
					);			
					$MyException->setParams($array);
					throw $MyException;
				}
				$row = $query->row_array();
				$query->free_result();
			
				return $row ;
			
			}catch(MyException $e){
				throw $e;
			}
		}
		
		public function getMenu()
		{
			$sql="SELECT * FROM  permissions";
		}
		
		
		public function del($ary)
		{
			try
			{
				if(empty($ary))
				{
					$MyException = new MyException();
					$array = array(
						'message' 	=>$error['message'] ,
						'status'	=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
					
				$bind = array(
					$ary['ad_id']
				);
				
				$sql ="SELECT  ar_id FROM admin_user WHERE ad_id = ?";
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'000'
					);			
					$MyException->setParams($array);
					throw $MyException;
				}
				$row = $query->row_array();
				$query->free_result();
				if($row['ar_id'] =='1')
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'017'
					);			
					$MyException->setParams($array);
					throw $MyException;
				}
			
				$sql="DELETE FROM admin_user WHERE ad_id=?";
				$this->db->query($sql, $bind); 
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
			}catch(MyException $e){
				throw $e;
			}
		}
		
		public function  insert($ary)
		{
			$output= array(
			'affected_rows'	=>0
			);
			try
			{
				$this->db->trans_begin();
				if(empty($ary))
				{
					$MyException = new MyException();
					$array = array(
						'message' 	=>$error['message'] ,
						'status'	=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				$sql ="INSERT INTO admin_user (ad_account, ad_passwd, ar_id)
					  VALUES (?,MD5(?),?)";
				$bind = array(
					$ary['ad_account'],
					$ary['ad_passwd'],
					$ary['ar_id'],
				);
				$this->db->query($sql, $bind); 
				$output['affected_rows'] += $this->db->affected_rows();
				$error = $this->db->error();
				if($error['message'] !="")
				{
					
					$status = '000';
					if($error['code'] == '1062')
					{
						$status = '015';
					}else if($error['code'] == '1452')
					{
						$status = '016';
					}
					
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				
				$this->db->trans_commit();
				return $output;
				
			}catch(MyException $e)
			{
				$this->db->trans_rollback();
				throw $e;
			}
		}
		
		public function getAdminByAccount($account)
		{
			
			try
			{
			
				$sql =" SELECT 
							ad_id, 
							ad_account, 
							ad_passwd ,
							ar_id ,
							ad_status
						FROM admin_user 
						WHERE ad_account =?";
				$bind =array(
					$account
				);
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'000'
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
		
		public function getAdminListAction($ary)
		{
			try
			{
				$sql ="	SELECT 
							per.pe_name,
							per.pe_func,
							per.pe_id,
							per.pe_control,
							per.pe_page
						FROM admin_user AS au 
							INNER JOIN admin_role_permissions_link AS link ON au.ar_id =  link.ar_id
							INNER JOIN permissions AS per ON link.pe_id = per.pe_id
						WHERE per.pe_parents_id = ? AND au.ad_id=? AND pe_type ='action'";
				$bind = array(
					$ary['pe_id'],
					$ary['ad_id']
				);
				$query = $this->db->query($sql, $bind);
				// echo $this->db->last_query();
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'001'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$rows = $query->result_array();
				return $rows;
			}catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function getAdminListButton($ary)
		{
			try
			{
				$sql ="	SELECT 
							per.pe_name,
							per.pe_func,
							per.pe_id,
							per.pe_control,
							per.pe_page
						FROM admin_user AS au 
							INNER JOIN admin_role_permissions_link AS link ON au.ar_id =  link.ar_id
							INNER JOIN permissions AS per ON link.pe_id = per.pe_id
						WHERE per.pe_parents_id = ? AND au.ad_id=? AND pe_type ='button'";
				$bind = array(
					$ary['pe_id'],
					$ary['ad_id']
				);
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'001'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$rows = $query->result_array();
				return $rows;
			}catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function getAdminPermissions($ary)
		{

			try
			{
				$sql ="	SELECT 
							per.pe_name,
							per.pe_func,
							per.pe_id,
							per.pe_control,
							per.pe_page,
							au.ad_id
						FROM admin_user AS au 
							INNER JOIN admin_role_permissions_link AS link ON au.ar_id =  link.ar_id
							INNER JOIN permissions AS per ON link.pe_id = per.pe_id
						WHERE au.ad_id = ? AND per.pe_id = ? ";
				$bind = array(
					$ary['ad_id'],
					$ary['pe_id']
				);
				$query = $this->db->query($sql, $bind);
			
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'001'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$row = $query->row_array();
				return $row;
			}catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function resetPassword($ary)
		{
			$output= array(
			'affected_rows'	=>0
			);
			try
			{
				$this->db->trans_begin();
				if(empty($ary))
				{
					$MyException = new MyException();
					$array = array(
						'message' 	=>$error['message'] ,
						'status'	=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				$sql ="UPDATE admin_user SET ad_passwd = MD5(?) WHERE ad_id =?";
				$bind = array(
					$ary['ad_passwd'],
					$ary['ad_id'],
				);
				$this->db->query($sql, $bind); 
				$output['affected_rows'] += $this->db->affected_rows();
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
				
				
				$this->db->trans_commit();
				return $output;
				
			}catch(MyException $e)
			{
				$this->db->trans_rollback();
				throw $e;
			}
		}
		
		public function setLock($ary)
		{
			$output= array(
				'affected_rows'	=>0
			);
			try
			{
				$this->db->trans_begin();
				if(empty($ary))
				{
					$MyException = new MyException();
					$array = array(
						'message' 	=>$error['message'] ,
						'status'	=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				$sql ="UPDATE admin_user SET ad_status = ? WHERE ad_id =?";
				$bind = array(
					$ary['ad_status'],
					$ary['ad_id'],
				);
				$this->db->query($sql, $bind); 
				$output['affected_rows'] += $this->db->affected_rows();
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
				
				
				$this->db->trans_commit();
				return $output;
				
			}catch(MyException $e)
			{
				$this->db->trans_rollback();
				throw $e;
			}
		}
		
		public function getUserById($id)
		{
			$output =array();
			try
			{
				$sql ="SELECT ad_account ,ad_id,ad_status FROM admin_user WHERE ad_id =?";
				$bind=array($id);
				$query = $this->db->query($sql,$bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'001'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$row = $query->row_array();
				$query->free_result();
				$output['row'] =$row ;
				return 	$output  ;
			}catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function getList($ary)
		{
			try
			{
				if(empty($ary))
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				$fields = join(',' ,$ary['fields']);
				
				$sql ="	SELECT ad_id AS id," 
						.$fields.	
						" FROM
							admin_user AS au INNER JOIN admin_role AS ar ON au.ar_id = ar.ar_id
						";
				$ary['sql'] =$sql;
				$output = $this->getListFromat($ary);
				return 	$output  ;
			}catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function getRoleList()
		{
			try
			{
				$sql ="	SELECT 
							ar_id ,
							ar_id AS value,
							ar_name,
							ar_name AS text
						FROM  admin_role  WHERE ar_id !=1";
				$query = $this->db->query($sql);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'001'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$rows = $query->result_array();
				return $rows;
			}catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function getAdminMenuList($ary)
		{
			try
			{
			
				$sql ="	SELECT 
							per.pe_name,
							per.pe_func,
							per.pe_id,
							per.pe_control,
							per.pe_page,
							per.pe_order_id ,
							per.pe_icon 
						FROM admin_user AS au 
							INNER JOIN admin_role_permissions_link AS link ON au.ar_id =  link.ar_id
							INNER JOIN permissions AS per ON link.pe_id = per.pe_id
						WHERE per.pe_parents_id = 0 AND  au.ad_id =? AND link.ar_id =?
						GROUP BY pe_id
						ORDER BY per.pe_order_id DESC
						";
				$bind =array(
					$ary['ad_id'],
					$ary['ar_id'],
				);
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
	
				$output['list'] = $query->result_array();
				if(!empty($output['list']))
				{
					foreach($output['list'] as $key => &$value)
					{
						$sql="	SELECT 
									pe_name,
									pe_func,
									pe_id,
									pe_control,
									pe_page,
									pe_icon
								FROM permissions WHERE pe_parents_id =? AND pe_type='menu' ORDER BY pe_order_id DESC";
						$bind = array(
							$value['pe_id']
						);
						$query = $this->db->query($sql, $bind);
						$error = $this->db->error();
						if($error['message'] !="")
						{
							$MyException = new MyException();
							$array = array(
								'message' 	=>$error['message'] ,
								'type' 		=>'db' ,
								'status'	=>'001'
							);
							
							$MyException->setParams($array);
							throw $MyException;
						}
						$child = $query->result_array();
						$value['child'] = $child;
					}
				}
				$query->free_result();
				return $output;
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
	}
?>