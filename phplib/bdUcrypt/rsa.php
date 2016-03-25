<?PHP

class mycrypt {  
 
    public $pubkey;  
    public $privkey;  
 
    function __construct() {  
                $this->pubkey = file_get_contents('/usr/home/xiaochen/keys/public.key');
                $this->privkey = file_get_contents('/usr/home/xiaochen/keys/private.key');
    }  
 
    public function encrypt($data) {  
        if (openssl_public_encrypt($data, $encrypted, $this->pubkey))  
            $data = base64_encode($encrypted);  
        else  
            throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');  
 
        return $data;  
    }  
 
    public function decrypt($data) {  
        if (openssl_private_decrypt(base64_decode($data), $decrypted, $this->privkey))  
            $data = $decrypted;  
        else  
            $data = '';

        return $data;
    }

}

function curl( $arrData ){
    $options = array(
        CURLOPT_URL => 'http://intra.skywalker.hxfilm.com/rest/v1/login',
        CURLOPT_HEADER => false,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $arrData,
    );

    $ch = curl_init();
    curl_setopt_array( $ch , $options );
    $result = curl_exec($ch);
    curl_close( $ch );
    return $result;
}


$rsa = new mycrypt();
$arrInput = array(
    'username' => 'admin',
    'password' => 'admin1234',
    'client' => 'client',
    'timestmp' => time(),
);




$strInput = json_encode( $arrInput );

#print_r( $strInput );

#echo "\n";
$data =  $rsa -> encrypt( $strInput );
#echo $data ;
#echo "\n";
$ret = curl( array( 'data'=>$data) );
#print_r( $ret );
#echo $rsa -> decrypt($data);
#echo "\n";

