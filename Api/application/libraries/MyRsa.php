<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MyRsa
{
	private $CI ;

	
	public function __construct() 
	{
		$this->CI =& get_instance();

	}
	
	
	private static $PRIVATE_KEY = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQCoCzqpvGqdrXyzGn3Rvvr1QPaCFAAiS+eAxsdfAEA+OjqQLkzz
uk96amsJqUA0eitAQDsJoQFQVaUQ1j1nZsAFTR3eCuiEk3f+3x1KZGNPPs2evUYW
AposUCk2qZm71+8zZe+6t8YZKtwz8xJFI5p7cqmawjYBc11oCMtKOVHL2QIDAQAB
AoGBAIW/ukdSwgESDkh3c2E4AoKl3A4YYSrrAy2KCTFh/8AHlIkhcPokdhHXFa6w
2XtrKXWQKe02Cten8yN9gY4FSBsl0Mab5cjoZfxcm8HZZFsCoqy+duKNygxyWILU
Asom0+gcGpahZaIsGWiQc5KRMuBwIlZBwN8icc4hXtpayQJJAkEA0/rpr6xT4Y/k
Tol9H+SI34asQRDTM88Zfkeeutt63MUW/ZZU7KaO4HSn/FA30nIQvM2rShyLl25i
UMf+fe3iiwJBAMrwnkA4gWNMlQLZhQTwOKjsyM17mqxqiUMwAR0dI+O+hxVOV3j8
NMxdX+IB2Sc9AHs1DxWsU5OgpueGdnN+i6sCQQC3BliIhmjyQzPjn5A6Xi7TmErX
7vf8Lp8bSilBuskNHtqn3wm6PmD0aaS1FGnuOPA8o/N2DMl12SfoCZWxExjbAkBd
jbcmW+Yp5K/89FHCCQvVs/KN56FSQnqsooCg70IQR1D2nXrtpzafz5vYEIoO8Kw8
ICWoFB0jPTg0G2SXsYqpAkBRLRyocI/c7tucQh7kYS2T5jkChJQfBj6aO1PdC1kM
+cs2pAYJ7eE106C1YGvUfP1yy6fo8wMgzW7e54fQz/8/
-----END RSA PRIVATE KEY-----';    
    private static $PUBLIC_KEY = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCoCzqpvGqdrXyzGn3Rvvr1QPaC
FAAiS+eAxsdfAEA+OjqQLkzzuk96amsJqUA0eitAQDsJoQFQVaUQ1j1nZsAFTR3e
CuiEk3f+3x1KZGNPPs2evUYWAposUCk2qZm71+8zZe+6t8YZKtwz8xJFI5p7cqma
wjYBc11oCMtKOVHL2QIDAQAB
-----END PUBLIC KEY-----';   
	private static $padding = OPENSSL_PKCS1_PADDING;
    /**     
     * 获取私钥     
     * @return bool|resource     
     */    
    private static function getPrivateKey() 
    {        
        $privKey = self::$PRIVATE_KEY;       

        return openssl_pkey_get_private($privKey, "phrase");    
    }    

    /**     
     * 获取公钥     
     * @return bool|resource     
     */    
    private static function getPublicKey()
    {        
        $publicKey = self::$PUBLIC_KEY;        
		
        return openssl_pkey_get_public($publicKey);    
    }    

    /**     
     * 私钥加密     
     * @param string $data     
     * @return null|string     
     */    
    public static function privateEncrypt($data = '')    
    {        
        if (!is_string($data)) {            
            return null;       
        }        
        return openssl_private_encrypt($data,$encrypted,self::getPrivateKey(), OPENSSL_PKCS1_PADDING) ? base64_encode($encrypted) : null;    
    }    

    /**     
     * 公钥加密     
     * @param string $data     
     * @return null|string     
     */    
    public static function publicEncrypt($data = '')   
    {        
        if (!is_string($data)) {            
            return null;        
        }        
        return openssl_public_encrypt($data,$encrypted,self::getPublicKey(), OPENSSL_PKCS1_PADDING) ? base64_encode($encrypted) : null;    
    }    

    /**     
     * 私钥解密     
     * @param string $encrypted     
     * @return null     
     */    
    public static function privateDecrypt($encrypted = '')    
    {        
        if (!is_string($encrypted)) {            
            return null;        
        }        
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, self::getPrivateKey(), OPENSSL_PKCS1_PADDING)) ? $decrypted : null;    
    }    

    /**     
     * 公钥解密     
     * @param string $encrypted     
     * @return null     
     */    
    public static function publicDecrypt($encrypted = '')    
    {        
        if (!is_string($encrypted)) {            
            return null;        
        }        
		return (openssl_public_decrypt(base64_decode($encrypted), $decrypted, self::getPublicKey(), OPENSSL_PKCS1_PADDING)) ? $decrypted : null;    
    }

}