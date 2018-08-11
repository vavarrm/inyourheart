<?php
	class Order_Model extends CI_Model 
	{
		private $ci;
		private $khrtousd ;
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
			$this->ci =& get_instance();
			$this->khrtousd = $this->ci->config->item('khrtousd');
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
				$status ='200';
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
		
		public function delMeals($ary)
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
				
				$bill = $this->getRowByCode($ary['code']);
				if(empty($bill))
				{
					$status='002';
					$MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
				}
				
				if(!empty($ary['meals']))
				{
					foreach($ary['meals'] as $value)
					{
						if($value['id'] =="")
						{
							continue;
						}
						$sql = "DELETE FROM sale_detailed WHERE code=? AND  id=?";
						$bind = array(
							$ary['code'],
							$value['id'],
						);
						$query = $this->db->query($sql, $bind);
						// echo $this->db->last_query();
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
				}
				
				
				$status ='200';
				$output['status']=$status;
				$this->db->trans_commit();
				$output['affected_rows'] =$affected_rows ;
				$output['code'] = $ary['code'];
				return $output;
				
			}catch(MyException $e)
            {
				$this->db->trans_rollback();
                throw $e;
            }
		}
		
		public function getRowByCode($code,$status="")
		{
			if($code =="")
			{
				$MyException = new MyException();
				$array = array(
					'el_system_error' 	=>'no setParams' ,
					'status'	=>$status
				);
				$MyException->setParams($array);
				throw $MyException;
			}
			$sql = "SELECT * FROM sale WHERE code =?";
			$bind=array($code);
			if($status!="")
			{
				$sql .=" AND status = ?";
				$bind[] = $status;
			}
			$query = $this->db->query($sql,$bind);
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
			$row = $query->row_array();
			$query->free_result();
			return $row;
			
		}
		
		public function getSaleDetailedByCode($code,$status="")
		{
			if($code =="")
			{
				$MyException = new MyException();
				$array = array(
					'el_system_error' 	=>'no setParams' ,
					'status'	=>$status
				);
				$MyException->setParams($array);
				throw $MyException;
			}
			$sql = "SELECT 
						sd.*,
						concat(me.kh_name,' ',me.en_name,' ',me.zh_name) AS full_name,
						sa.number,
						sa.delivery
					FROM 
						sale_detailed AS sd INNER JOIN sale AS sa  ON sd.code = sa.code
						LEFT JOIN menu AS me ON sd.me_id = me.id
					WHERE sd.code =? ";
			$bind=array($code);
			if($status!="")
			{
				$sql .=" AND sa.status = ?";
				$bind[] = $status;
			}
			$query = $this->db->query($sql,$bind);
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
			$rows = $query->result_array();
			$query->free_result();
			
			$sql =sprintf("	SELECT 
						SUM(t.quantity*t.unit_price) AS total_usd ,
						FLOOR(SUM(t.quantity*t.unit_price)*%d) AS total_riel ,
						t.number,
						t.delivery
					FROM(%s) AS t",$this->khrtousd ,$this->db->last_query());
			$bind = array($code);
			$query = $this->db->query($sql ,$bind);
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
			$row = $query->row_array();
			$query->free_result();
			$output['list'] = $rows;
			$output['info'] = $row;
			return $output;
			
		}
		
		public function getTotalByCode($code)
		{
			if($code =="")
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
							SUM(amount) AS total ,
							SUM(amount)/%d AS total_riel 
						FROM sale_detailed WHERE code =?";
			$sql = sprintf($sql,$this->khrtousd);
			$bind=array($code);
			$query = $this->db->query($sql,$bind);
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
			$row = $query->row_array();
			$query->free_result();
			return $row;
			
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
				
				$bill = $this->getRowByCode($ary['code']);
				
				if(empty($bill))
				{
					$status='002';
					$MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
				}
				
				if($bill['status'] == 'checkout')
				{
					$status='003';
					$MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'' ,
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
					
					if($value['id'] !="")
					{
						$sql = "UPDATE  sale_detailed
								SET
									unit_price = ?,
									original_price =?,
									quantity = ?
								WHERE code = ?  AND id = ?";
						$bind = array(
							$value['unit_price'],
							$value['original_price'],
							$value['quantity'],
							$ary['code'],
							$value['id'],
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
						$id = $value['id'];
						
					}else
					{
					
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
					}
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
				
				$status ='200';
				$output['status']=$status;
				$this->db->trans_commit();
				$output['affected_rows'] =$affected_rows ;
				$output['code'] = $ary['code'];
				return $output;
				
			}catch(MyException $e)
            {
				$this->db->trans_rollback();
                throw $e;
            }
		}
		
		public function getBillForCheckBill($code)
		{
			try
            {
                $sql ="	SELECT 
							*,
							concat(me.kh_name,' ',me.en_name,' ',me.zh_name) AS full_name ,
							md5(concat('menuimg',me.id)) AS img,
							(sd.quantity*sd.unit_price) AS subtotal
						FROM 
							sale_detailed AS sd  LEFT JOIN menu AS me 
							ON sd.me_id = me.id
						WHERE sd.code = ? ";
				$bind = array($code);
				$query = $this->db->query($sql, $bind);
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
				$rows = $query->result_array();
				$query->free_result();
				
				$sql ="SELECT SUM(unit_price*quantity) AS total ,  FLOOR(SUM(unit_price*quantity)*%d)AS total_riel FROM sale_detailed WHERE code = ?  ";
				$sql = sprintf($sql,$this->khrtousd);
				$bind = array($code);
				$query = $this->db->query($sql, $bind);
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
				$row = $query->row_array();
				$query->free_result();
				
				
				$sql = "SELECT * FROM sale WHERE code =?";
				$bind = array($code);
				$query = $this->db->query($sql, $bind);
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
				$data = $query->row_array();
				$query->free_result();
				
				$output['list'] = $rows;
				$output['info'] = array_merge($data,$row);
				
				return $output;
            }catch(MyException $e)
            {
                throw $e;
            }
		}
		
		public function getListByStatusAndDelivery($status='making',$delivery="no")
		{
			try
            {
                $sql ="SELECT * FROM sale WHERE status = ? AND delivery=? ORDER BY CODE DESC";
				$bind = array(
					$status,
					$delivery
				);
				$query = $this->db->query($sql, $bind);
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
				$rows = $query->result_array();
				$query->free_result();
				return $rows;
            }catch(MyException $e)
            {
                throw $e;
            }
		}
		
		public function getUsingNumber()
		{
			try
            {
                $sql ="SELECT group_concat(number) AS using_number FROM sale WHERE status = 'making'";
				$query = $this->db->query($sql);
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
				$row = $query->row_array();
				$query->free_result();
				return $row;
            }catch(MyException $e)
            {
                throw $e;
            }
		}
		
		public function checkBill($ary)
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
				
				$bill = $this->getRowByCode($ary['code']);
				
				if(empty($bill))
				{
					$status='002';
					$MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
				}
				
				if($bill['status'] == 'checkout')
				{
					$status='003';
					$MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
				}
				
				if($ary['discount'] <= 0  || $ary['discount'] >1)
				{
					$status='004';
					$MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
				}
				
				
				$data = $this->getTotalByCode($ary['code']);
				
				if($ary['pay_amount_usd'] !=0 && $ary['pay_amount_usd']< $data['total']*ary['discount'])
				{
					$status='007';
					$MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
				}
				
				if($ary['pay_amount_riel'] !=0 && $ary['pay_amount_riel']< $data['total_riel'])
				{
					$status='007';
					$MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
				}
				
				
				if(empty($data) || $data['total'] <=0)
				{
					$status='005';
					$MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'' ,
                        'status'	=>$status
                    );
                    $MyException->setParams($array);
                    throw $MyException;
				}
				
				$sql ="	UPDATE 
							sale 
						SET 
							original_total	 =? , 
							discount=? , 
							pay_amount_usd =?,
							pay_amount_riel =?
						WHERE code=?";
				$bind =array(
					$data['total'],
					$ary['discount'],
					$ary['pay_amount_usd'],
					$ary['pay_amount_riel'],
					$ary['code'],
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
				
				$sql ="	UPDATE 
							sale 
						SET 
							total	 	=(discount *original_total)  , 
							pay_change	= ? ,
							status=	'checkout' ,
							checkbill_datetime = NOW()
						WHERE code=?";
				$bind =array(
					$ary['pay_change'],
					$ary['code']
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
				
				
				$sql ="INSERT INTO account (code,type,usd,operator)";
				$sql .="SELECT code,'sale',total,'+' FROM sale WHERE code =?";
				$bind =array(
					$ary['code']
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
				$affected_rows = $this->db->affected_rows();
				
				
				$status ='200';
				$output['status']=$status;
				$this->db->trans_commit();
				$output['affected_rows'] =$affected_rows ;
				$output['code'] = $ary['code'];
				
				return $output;
				
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