<?php
	class Cron_Model extends CI_Model 
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
	
		public function getSerialNumber($type)
		{
			try
			{
				$query = $this->db->query("CALL get_serial_number('order')");
				$row  = $query->row_array();
				$query->next_result(); 
				return $row ;
				
			}catch(MyException $e)
			{
				throw $MyException;
			}
		}
	}
?>