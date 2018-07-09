<?php
	class Qrcode_Model extends CI_Model 
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

		public function getRow($id)
		{
			try
			{
				$bind = array(
					$id
				);
				
				$sql = "SELECT * FROM qrcode WHERE id =?";
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
			}	
			catch(MyException $e)
			{
				$this->db->trans_rollback();
				throw $e;
			}
		}
		
		public function del($id)
		{
			$output = array();
			try
			{
				$bind = array(
					$id
				);
				
				$sql = "SELECT * FROM qrcode WHERE id =?";
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
				if(empty($row))
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>'cant get Qrcode row',
						'status'	=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				
				$sql ="	DELETE FROM qrcode WHERE id=?";
				$this->db->query($sql, $bind);
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
				
				$path = IMAGEPATH.'qrcode'.DIRECTORY_SEPARATOR;
				if(is_file($path.$row['image_path'].$row['image_name']))
				{
					  unlink($path.$row['image_path'].$row['image_name']);
				}
				return $output ;
			}	
			catch(MyException $e)
			{
				$this->db->trans_rollback();
				throw $e;
			}
		}
		
		public function insert($ary)
		{
			$output = array();
			try
			{
				$sql ="	REPLACE INTO qrcode(
							image_name,
							data,
							title,lat,lang
						)VALUES(?,?,?,?,?)";
				$bind =array(
					$ary['filename'],
					$ary['data'],
					$ary['title'],
                    $ary['lat'],
                    $ary['lang'],

				);
				
				$this->db->query($sql, $bind);
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
				
				$affected_rows = $this->db->affected_rows();
				$output['affected_rows'] = $affected_rows;
				
				return $output ;
			}	
			catch(MyException $e)
			{
				$this->db->trans_rollback();
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
				$sql ="	SELECT  id," 
						.$fields.	
						" FROM
						qrcode AS t";
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