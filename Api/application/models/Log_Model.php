<?php
	class Log_Model extends CI_Model 
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
		
		public function  error_log($ary)
		{
			$sql="INSERT INTO  error_log  
					(el_message,el_class,el_function,el_system_error) 
					VALUES(?,?,?,?)";
			$bind = array(
				$ary['message'],
				$ary['class'],
				$ary['function'],
				$ary['el_system_error'],
			);
			
			$query = $this->db->query($sql, $bind);
		}
	}
?>