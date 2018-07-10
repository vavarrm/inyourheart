<?php
	class Purchase_Model extends CI_Model 
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
		
		
		public function delBycode($code)
		{
			try
            {
				$status='000';
				$this->db->trans_begin();
				if($code == '')
                {
                    $MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'no setParams' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
                }
				
				$sql = "UPDATE  
							purchase
						SET
							is_del = 'true'
						WHERE code =?";
				$bind = array(
					$code
				);
				$query = $this->db->query($sql,$code);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'			=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				$affected_rows = $this->db->affected_rows();
				
				if($affected_rows == 0)
				{
					$this->db->trans_rollback();
					return $output['affected_rows'] =$affected_rows ;
				}
				
				
				
				$sql = "UPDATE  
							account
						SET
							is_del = 'true'
						WHERE code =?";
				$bind = array(
					$code
				);
				$query = $this->db->query($sql,$code);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'			=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				$affected_rows = $this->db->affected_rows();
				
				if($affected_rows == 0)
				{
					$this->db->trans_rollback();
					return $output['affected_rows'] =$affected_rows ;
				}
				
				$affected_rows = $this->db->affected_rows();
				$this->db->trans_commit();
				$output['affected_rows'] =$affected_rows ;
				return $output;
				
			}catch(MyException $e)
            {
				$this->db->trans_rollback();
                throw $e;
            }
		}
		
		public function getDetailsByCode($code)
		{
			$status = '000';
			if(empty($code))
			{
				$MyException = new MyException();
				$array = array(
					'el_system_error' 	=>'no setParams' ,
					'status'	=>$status
				);
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$sql ="	SELECT 
						pd.*,
						pd.name AS full_name	
					FROM 
							purchase_detailed  AS  pd
					WHERE pd.code =?";
			$bind= array(
				$code
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
			$rows = $query->result_array();
			$query->free_result();
			return $rows;
		}
		
		public function add($ary,$admin)
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
				
				if(empty($admin))
                {
                    $MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'admin data empty' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
                }
				
				if($ary['pay_amount_usd'] <=0 && $ary['pay_amount_khr'] <=0)
				{
					$status ="008";
					$MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'Please Keyin Pay Amount' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
				}
				
				$sql = "SELECT  
							CONCAT('P',DATE_FORMAT(NOW(),'%Y%m%d') , LPAD(RIGHT(`code`,6)  + 1 ,6,0) ) AS code 
						FROM account  
						WHERE DATE_FORMAT(NOW(),'%m-%d-%Y') = DATE_FORMAT(`add_datetime`,'%m-%d-%Y') AND `type` = 'purchase' ORDER BY code DESC LIMIT 1";
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
					$sql="SELECT  CONCAT('P',DATE_FORMAT(NOW(),'%Y%m%d') , LPAD(RIGHT(1,6)  ,6,0) ) AS code";
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
				
				foreach($ary['item'] as  $key => $value)
				{
					
					if($value == 'null')
					{
						continue;
					}
					
					if(
						$ary['subtotal'][$key] <=0 ||
						$ary['quantity'][$key] <=0 
					)
					{
						$MyException = new MyException();
						$array = array(
							'el_system_error' 	=>'subtotal,  quantity lose zero ' ,
							'status'	=>$status
						);
						
						$MyException->setParams($array);
						throw $MyException;
					}
					
					$sql = "INSERT INTO purchase_detailed
									(name,date,unit_price,amount,currency,unit,quantity,code)
							VALUES 	(?,NOW(),?,?,?,?,?,?)
							";
					$bind = array(
						$value,
						$ary['price'][$key],
						$ary['subtotal'][$key],
						$ary['currency'][$key],
						$ary['unit'][$key],
						$ary['quantity'][$key],
						$row['code']
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
					$affected_rows += $this->db->affected_rows();
					
					
				}
				
				if($affected_rows == 0)
				{
					return $output['affected_rows'] =$affected_rows ;
				}
				
				
				$sql = "INSERT INTO purchase	
								(khr,usd,code,pay_amount_usd,pay_amount_khr)
						VALUES 	(?,?,?,?,?)";
				$bind = array(
					$ary['khr_total'],
					$ary['usd_total'],
					$row['code'],
					$ary['pay_amount_usd'],
					$ary['pay_amount_khr']
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
				
				$sql = "INSERT INTO account	
								(type,khr,usd,code)
						VALUES 	('purchase',?,?,?)";
				$bind = array(
					$ary['khr_total'],
					$ary['usd_total'],
					$row['code']
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
				
				$this->ci->load->model('Account_Model', 'account');
				$ary = array(
					'code'	 	=>$row['code'],
					'type'		=>'insert',
					'ad_id'		=>$admin['ad_id']
				);
				$this->account->log($ary);
				
				
				$this->db->trans_commit();
				$output['affected_rows'] =$affected_rows ;
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
				$sql  = sprintf($sql,$this->khrtousd);
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