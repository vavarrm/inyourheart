<?php
	class Menu_Model extends CI_Model 
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
		
		public function add($ary)
		{
			try
			{
				if(empty($ary))
                {
                    $MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'no setParams' ,
                        'status'	=>'000'
                    );
                    $MyException->setParams($array);
                    throw $MyException;
                }
				
				$sql = "INSERT INTO  menu (zh_name, en_name, kh_name,unit_price,ca_id)
						VALUES (?,?,?,?,?)";
				
				$bind = array(
					$ary['zh_name'],
					$ary['en_name'],
					$ary['kh_name'],
					$ary['unit_price'],
					$ary['category'],
				);
				
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$status = '000';
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
			
			}
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function edit($ary)
		{
			try
			{
				if(empty($ary))
                {
                    $MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'no setParams' ,
                        'status'	=>'000'
                    );
                    $MyException->setParams($array);
                    throw $MyException;
                }
				
				$sql = "UPDATE   menu  SET zh_name =?, en_name=?,kh_name=?,unit_price=?,ca_id=? WHERE id = ?";
				
				$bind = array(
					$ary['zh_name'],
					$ary['en_name'],
					$ary['kh_name'],
					$ary['unit_price'],
					$ary['category'],
					$ary['id'],
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
				
				$affected_rows = $this->db->affected_rows();
				$output['affected_rows'] =$affected_rows ;
				return $output;
			
			}
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function getRowByID($id)
		{
			try
			{
				if(empty($id))
                {
                    $MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'no setParams' ,
                        'status'	=>'000'
                    );
                    $MyException->setParams($array);
                    throw $MyException;
                }
				
				$sql = "SELECT * FROM  menu WHERE id = ?";
				$bind = array(
					$id
				);
				$query = $this->db->query($sql,$bind);
				// echo $this->db->last_query();
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
		
		public function del($id)
		{
			try
			{
				if(empty($id))
                {
                    $MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'no setParams' ,
                        'status'	=>'000'
                    );
                    $MyException->setParams($array);
                    throw $MyException;
                }
				
				$sql = "DELETE FROM  menu WHERE id = ?";
				
				$bind = array(
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
				
				$affected_rows = $this->db->affected_rows();
				$output['affected_rows'] =$affected_rows ;
				return $output;
			
			}
			catch(MyException $e)
			{
				throw $e;
			}
		}
		

		public function getMenuIndexByCategory()
		{
			$status='000';
			$sql = "SELECT id,  concat(kh_name,' ',en_name,' ',zh_name) AS full_name FROM category ORDER BY id";
			$query = $this->db->query($sql);
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
			
			$rows= $query->result_array();
			if(!empty($rows))
			{
				foreach($rows as $row)
				{
					$sql = "SELECT id ,unit_price,  concat(kh_name,' ',en_name,' ',zh_name) AS full_name ,md5('menuimg'+id) AS img FROM 	menu WHERE ca_id  = ? AND status ='sale_on'"; 
					
					$bind = array(
						$row['id']
					);
					$query = $this->db->query($sql,$bind);
					$error = $this->db->error();
					if($error['message'] !="")
					{
						$status='000';
						$MyException = new MyException();
						$array = array(
							'el_system_error' 	=>$error['message'] ,
							'status'	=>$status
						);
						
						$MyException->setParams($array);
						throw $MyException;
					}
					$menus= $query->result_array();
					$output['menu'][$row['id']]= array(
						'list'	=>$menus
					);
				}
			}
			$output['category'] = $rows;
			$query->free_result();
			return $output;
			// var_dump($output);
		}
		
		public function getList($ary)
        {

            try
            {
                if(empty($ary))
                {
                    $MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'no setParams' ,
                        'status'	=>'000'
                    );
                    $MyException->setParams($array);
                    throw $MyException;
                }
                if(!empty($ary['fields']))
                {
                    foreach($ary['fields'] as $value)
                    {
                        $temp[] = $value['field'];
                    }
                }
                $fields = join(',' ,$temp);

                $sql ="	SELECT "
                    . $fields.
                    " FROM menu AS t LEFT JOIN category AS ca ON t.ca_id = ca.id";

                $ary['sql'] =$sql;
                $output = $this->getListFromat($ary);
				
                return 	$output  ;
            }catch(MyException $e)
            {
                throw $e;
            }
        }
		
		
		
		
	}
?>