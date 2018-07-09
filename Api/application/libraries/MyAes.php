<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MyAes
{
	private $CI ;
	private $encryption_key;
	private $method ;
	private $iv;
	
	public function __construct() 
	{
		$this->CI =& get_instance();
		$this->method = 'aes-256-cbc';
		$this->encryption_key  ="9F402C02562E26AA1A178441E8E4BD582CF5039B681587CB";
		$this->iv = '47C74E0AA79B96171F57CFBC77790C29';
	}
	
	public function encrypt($data)
	{
		$encrypted = openssl_encrypt($data, $this->method, $this->encryption_key, 0, $this->iv);
		$encrypted = $encrypted;
		return $encrypted ;
	}


	public function decrypt($encrypted)
	{

		$decrypted = openssl_decrypt($encrypted, $this->method, $this->encryption_key, 0, $this->iv);
		return $decrypted;
	}
	
	
	private function set_encryption_key($key)
	{
		$this->encryption_key  = $key;
	}
	
	private function set_iv($iv)
	{
		$this->iv  = $iv;
	}
	
	public function get_iv()
	{
		return $this->iv;
	}
}