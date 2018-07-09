<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."third_party/phpqrcode/qrlib.php");
class MyQrcode 
{
	private $CI ;

	
	public function __construct() 
	{
		$this->CI =& get_instance();
	}
 

}