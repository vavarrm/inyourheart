<?php
	class Account_Model extends CI_Model 
	{
		private $ci;
		function __construct()
		{
			
			parent::__construct();
			$this->load->database();
			$this->ci =& get_instance();
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

		public function log($ary)
		{
			try
            {
				$status = '000';
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
				
				$sql ="	INSERT  
							INTO account_log (ac_code,type,ad_id)
							VALUES(?,?,?)
							
						";
				$bind= array(
					$ary['code'],
					$ary['type'],
					$ary['ad_id'],
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
			}catch(MyException $e)
            {
                throw $e;
            }
		}
		
		
		public function getDetailsByCode($code)
		{
			try
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
				$khrtousd= $this->ci->config->item('khrtousd');
				$sql ="	SELECT  
							*,
							DATE_FORMAT(add_datetime,'%Y-%m-%d') as add_date,
							ROUND((usd + (khr/".$khrtousd.")),2) AS usd_total,
							ROUND((khr + (usd*".$khrtousd."))) AS khr_total
						
						FROM 
							account
						WHERE code =?";
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
				$row = $query->row_array();
				$query->free_result();
				return $row;
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
                    " FROM account AS t ";

                $ary['sql'] =$sql;
                $output = $this->getListFromat($ary);

                return 	$output  ;
            }catch(MyException $e)
            {
                throw $e;
            }
        }
		
		public function getcashFlow()
		{
			try
            {
				$status = '000';
				$khrtousd= $this->ci->config->item('khrtousd');
				$sql ="	SELECT
							`type`,
							SUM(`khr`) AS khr,
							SUM(`usd`) AS usd,
							ROUND(SUM(`khr`/%d+`usd`),2) AS  total_usd ,
							ROUND(SUM(`khr`+`usd`*%d)) AS  total_khr,
							operator
						 FROM `account` 
						 WHERE is_del ='false'
						 GROUP BY `type`";
						 

				$sql = sprintf($sql,$khrtousd,$khrtousd);
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
				$rows = $query->result_array();
				$output['list'] = $rows;
				$query->free_result();
				
				
				$sql="	SELECT ROUND((t.khr/4000 + t.usd),2)  total_usd , ROUND((t.khr + t.usd*4000)) AS total_khr FROM (SELECT 
							SUM(CASE `operator` 
								WHEN '+' THEN `khr`
								WHEN '-' THEN `khr`*-1
							END) AS khr,
							SUM(CASE `operator` 
								WHEN '+' THEN `usd`
								WHEN '-' THEN `usd`*-1
							END) AS usd
						FROM account
						 WHERE is_del ='false'
						) AS t";
				
				$sql = sprintf($sql,$khrtousd,$khrtousd);
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
				$row = $query->row_array();
				$output['info'] = $row;
				$query->free_result();
				
				return $output;
			}catch(MyException $e)
            {
                throw $e;
            }
		}

	}
?>