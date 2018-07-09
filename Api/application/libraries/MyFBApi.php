<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MyFBApi
{
	private $CI ;
	private $fb;
	public function __construct() 
	{
		require_once (APPPATH.'/third_party/Facebook/autoload.php');

		$this->CI = & get_instance();
		$fb = new Facebook\Facebook ([
			'app_id' => $this->CI->config->item('fb_app_id'),
			'app_secret' =>$this->CI->config->item('fb_app_secret'),
			'default_graph_version' => $this->CI->config->item('fb_version')
		]);
		$this->fb = $fb;
		//
		// exit;
		// return $this ;
	}
	
	public function get($url, $access_token)
	{
		$response = $this->fb->get($url, $access_token);
		return $response;
	}
	
	
}