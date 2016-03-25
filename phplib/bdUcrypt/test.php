<?PHP

require_once 'bdUcrypt.class.php';

$post_get['BDUSS'] = 'WF2Mmx3WjlQTXdhbkczV3BTZTl1WVBLQzA5YnpUaVQ5cVdMczlZbHFmZkI4bnRWQVFBQUFBJCQAAAAAAAAAAAEAAABOuewLZGV2X3Rlc3QzAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMFlVFXBZVRVS';
$bduss_sk = '@a#$%a%';
$cookie = bdUcrypt::decodeDeviceId($post_get['BDUSS'], $bduss_sk );
print_r ( $cookie );



