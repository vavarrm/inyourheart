<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mysmarty
{
	private $CI;
	private $randseedload;
    public function __construct()
    {
		require_once(APPPATH."third_party/smarty/libs/Smarty.class.php");
		$this->CI = & get_instance();
		$this->randseed = time().rand(1,9999);
		$this->smarty = new Smarty;
		
		$this->smarty->setTemplateDir(BASEPATH.'../application/views/');
		$this->smarty->setCompileDir(BASEPATH.'../application/views/templates_c');
		$this->smarty->left_delimiter ="<{";
		$this->smarty->right_delimiter = "}>";
		$template_dir = $this->smarty->getTemplateDir();
		return $this->smarty;
    }
	
	public function display($tpl)
	{

		$language_ary = $this->CI->language->load('main');
		$language_js_ary =$this->CI->language->load('js');
		
		$this->assign(array(
			'language'	=>$language_ary,
			'language_js_ary'		=>$language_js_ary,
			'randseed'				=>$this->randseed,
			'fb_app_id'				=>$fb_app_id,
			'fb_app_key'			=>$fb_app_key,
			'fb_version'			=>$fb_version,
	
		));
		$this->smarty->clearAllCache();
		$this->smarty->caching = false;
		$this->smarty->display($tpl);
	}
	
	public function displayFrame($tpl)
	{
		
		$language_ary = $this->CI->language->load('main');
		$language_js_ary =$this->CI->language->load('js');
		$fb_app_id =$this->CI->config->item('fb_app_id');
		$fb_app_key =$this->CI->config->item('fb_app_key');
		$fb_version =$this->CI->config->item('fb_version');
		$fb_version =$this->CI->config->item('fb_version');
		$google_map_api_key=$this->CI->config->item('google_map_api_key');
		$website =$this->CI->config->item('website');
		$this->CI->load->model('Food_Model', 'food');
		$category = $this->CI->food->getCategory();
		$this->assign(array(
			'language'	=>$language_ary,
			'language_js_ary'	=>$language_js_ary,
			'randseed'	=>$this->randseed,
			'content'	=>$tpl,
			'fb_app_id'=>$fb_app_id,
			'fb_app_key'=>$fb_app_key,
			'fb_version'=>$fb_version,
			'category'	=>$category,
			'website'	=>$website,
			'google_map_api_key'	=>$google_map_api_key
		));
		$this->smarty->clearAllCache();
		$this->smarty->caching = false;
		$this->smarty->display('Frontend/frame.tpl');
	}
	
	public function assign($var)
	{
		$this->smarty->assign($var);
	}
	public function fetch($tpl)
	{
		return $this->smarty->fetch($tpl);
	}
}