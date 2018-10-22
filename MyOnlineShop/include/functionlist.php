<?php
	function encryptDecrypt($key, $string, $decrypt){ 
	    if($decrypt){ 
	    	$src = array("_a","_b","_c");
	        $dist  = array("/","+","=");
	        $new  = str_replace($src,$dist,$string);
	        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($new), MCRYPT_MODE_CBC, md5(md5($key))), "12"); 
	        return $decrypted; 
	    }else{ 
	        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key)))); 
	        $src  = array("/","+","=");
        	$dist = array("_a","_b","_c");
        	$new  = str_replace($src,$dist,$encrypted);
	        return $new; 
	    } 
	}
?>