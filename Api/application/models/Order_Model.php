<?php
	class Order_Model extends CI_Model 
	{
		private $ci;
		private $khrtousd ;
		function __construct()
		{
			
			parent::__construct();
			$this->ci =& get_instance();
			$this->load->database();
			$query = $this->db->query("set time_zone = '+7:00'");
			$error = $this->db->error();
			$this->khrtousd = $this->ci->config->item('khrtousd');
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
				$status='000';
				$this->db->trans_begin();
				if(empty($ary))
                {
                    $MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'no setParams' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
                }
				
				
				$sql="SELECT COUNT(*) AS total FROM  sale WHERE number = ? AND status ='making'";
				$bind = array(
					$ary['number']
				);
				$query = $this->db->query($sql, $bind);
				// echo $this->db->last_query
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'],
						'status'			=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				
				
				$row = $query->row_array();
				$query->free_result();
				if($row['total'] > 0)
				{
					
					$status ='018';
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>'get Number error',
						'status'			=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
	
				}
				
				// $sql ="INSERT INTO  sale()"; 
				
				
				$sql = "SELECT  
							CONCAT('S',DATE_FORMAT(NOW(),'%Y%m%d') , LPAD(RIGHT(`code`,6)  + 1 ,6,0) ) AS code 
						FROM sale 
						WHERE DATE_FORMAT(NOW(),'%m-%d-%Y') = DATE_FORMAT(`add_datetime`,'%m-%d-%Y')  ORDER BY code DESC LIMIT 1";
				$query = $this->db->query($sql);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>'get code error' ,
						'status'			=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				$row = $query->row_array();
				$query->free_result();
				if(empty($row['code']))
				{
					$sql="SELECT  CONCAT('S',DATE_FORMAT(NOW(),'%Y%m%d') , LPAD(RIGHT(1,6)  ,6,0) ) AS code";
					$query = $this->db->query($sql);
					$error = $this->db->error();
					if($error['message'] !="")
					{
						$MyException = new MyException();
						$array = array(
							'el_system_error' 	=>'get code error' ,
							'status'			=>'000'
						);
						
						$MyException->setParams($array);
						throw $MyException;
					}
					$row = $query->row_array();
					$query->free_result();
				}
				
				
				
				
				foreach($ary['meals'] as  $key => $value)
				{
				
					
					if($value['me_id'] == '')
					{
						continue;
					}
					
					
					
					$sql = "INSERT  sale_detailed
									(code,me_id,unit_price,	original_price,quantity)
							VALUES 	(?,?,?,?,?)
							";
					$bind = array(
						$row['code'],
						$value['me_id'],
						$value['unit_price'],
						$value['original_price'],
						$value['quantity'],
					);
					$query = $this->db->query($sql, $bind);
					$error = $this->db->error();
					if($error['message'] !="")
					{
						$status ='000';
						$MyException = new MyException();
						$array = array(
							'el_system_error' 	=>$error['message'] ,
							'status'	=>$status
						);
						
						$MyException->setParams($array);
						throw $MyException;
					}
					$id = $this->db->insert_id();
					$sql ="UPDATE sale_detailed SET amount = unit_price* quantity WHERE id =?";
					$bind = array(
						$id
					);
					$query = $this->db->query($sql, $bind);
					$error = $this->db->error();
					if($error['message'] !="")
					{
						$status ='000';
						$MyException = new MyException();
						$array = array(
							'el_system_error' 	=>$error['message'] ,
							'status'	=>$status
						);
						
						$MyException->setParams($array);
						throw $MyException;
					}
					
					$affected_rows += $this->db->affected_rows();
					
					
				}
				
				if($affected_rows == 0)
				{
					return $output['affected_rows'] =$affected_rows ;
				}
				
				$sql = "INSERT INTO sale
								(code,number,delivery)
						VALUES 	(?,?,?)";
				$bind = array(
					$row['code'],
					$ary['number'],
					$ary['delivery'],
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
				$status ='100';
				$output['status']=$status;
				$this->db->trans_commit();
				$output['affected_rows'] =$affected_rows ;
				$output['code'] = $row['code'];
				return $output;
				
			}catch(MyException $e)
            {
				$this->db->trans_rollback();
                throw $e;
            }
		}
		
		public function addMore($ary)
		{
			try
            {
				$status='000';
				$this->db->trans_begin();
				if(empty($ary))
                {
                    $MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'no setParams' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
                }
				
				
				
				
				
				foreach($ary['meals'] as  $key => $value)
				{	
					if($value['me_id'] == '')
					{
						continue;
					}
					
					$sql = "INSERT  sale_detailed
									(code,me_id,unit_price,	original_price,quantity)
							VALUES 	(?,?,?,?,?)
							";
					$bind = array(
						$ary['code'],
						$value['me_id'],
						$value['unit_price'],
						$value['original_price'],
						$value['quantity'],
					);
					$query = $this->db->query($sql, $bind);
					$error = $this->db->error();
					if($error['message'] !="")
					{
						$status ='000';
						$MyException = new MyException();
						$array = array(
							'el_system_error' 	=>$error['message'] ,
							'status'	=>$status
						);
						
						$MyException->setParams($array);
						throw $MyException;
					}
					$id = $this->db->insert_id();
					$sql ="UPDATE sale_detailed SET amount = unit_price* quantity WHERE id =?";
					$bind = array(
						$id
					);
					$query = $this->db->query($sql, $bind);
					$error = $this->db->error();
					if($error['message'] !="")
					{
						$status ='000';
						$MyException = new MyException();
						$array = array(
							'el_system_error' 	=>$error['message'] ,
							'status'	=>$status
						);
						
						$MyException->setParams($array);
						throw $MyException;
					}
					
					$affected_rows += $this->db->affected_rows();
					
				}
				
				if($affected_rows == 0)
				{
					return $output['affected_rows'] =$affected_rows ;
				}
				
				$status ='100';
				$output['status']=$status;
				$this->db->trans_commit();
				$output['affected_rows'] =$affected_rows ;
				$output['code'] = $row['code'];
				return $output;
				
			}catch(MyException $e)
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

                $sql ="	SELECT 
						
					"
                    . $fields.
                    " FROM purchase AS t ";
                $ary['sql'] =$sql;
				// echo $sql;
                $output = $this->getListFromat($ary);

                return 	$output  ;
            }catch(MyException $e)
            {
                throw $e;
            }
        }
		
		
		
		
	}
?>