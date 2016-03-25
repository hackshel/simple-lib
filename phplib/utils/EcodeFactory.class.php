<?php

/**
 * 
 * @abstract 电子码生成和逆推工具类
 * @version  v1.0 2014-10-14
 */
class EcodeFactory
{
    public static function dictIntHashFunction($key)
    {
        $key = intval($key);
        $key = self::change(~ $key);
        $key = $key ^ self::change($key << 21);;        
        $key = $key ^ ($key >> 24);
        $key = $key ^ self::change($key << 9);
        $key = 0x165667b1 ^ $key;
        $key = $key ^ self::change($key << 5);
        $key = $key ^ ($key >> 28);

        return $key;
    }
    
    public static function getRetrivedValueFromHash($key)
    {
        $key = self::getValueBeforeRightShift($key, 28);
        $key = self::getValueBeforeLeftShift($key, 5);
        $key = 0x165667b1 ^ $key;
        $key = self::getValueBeforeLeftShift($key, 9);
        $key = self::getValueBeforeRightShift($key, 24);
        $key = self::getValueBeforeLeftShift($key, 21);
        $result = self::change(~ $key);
        
        return $result;
    }
    
    private static function getValueBeforeLeftShift($value, $shift_num)
    {
        $bin_value = str_pad(decbin($value), 39, '0' ,STR_PAD_LEFT);
        $shift_num = intval($shift_num);
        $key = substr($bin_value, -$shift_num);
        $result = $key;
        $remain_length = 39 -  $shift_num;

        if($remain_length > $shift_num)
        {
            $offset = 2*$shift_num;
        }
        
        while($remain_length > $shift_num)
        {;
            $key = decbin(bindec(substr($bin_value, -$offset, $shift_num)) ^ bindec($key));
            $new_key = str_pad($key, $shift_num, '0' ,STR_PAD_LEFT);
            $result = $new_key . $result;
            $offset += $shift_num;
            $remain_length -= $shift_num;
        }

        $key = bindec(substr($bin_value, 0, $remain_length)) ^ bindec($key);
        $result = substr(decbin($key), -$remain_length) . $result;
        
        return bindec($result);
    }
    
    private function getValueBeforeRightShift($value, $shift_num)
    {
        $bin_value = str_pad(decbin($value), 39, '0' ,STR_PAD_LEFT);
        $shift_num = intval($shift_num);
        $key = substr($bin_value, 0, $shift_num);
        $result = $key;
        $remain_length = 39 -  $shift_num;
        
        $offset = $shift_num;
        while($remain_length > $shift_num)
        {
            $key = decbin(bindec(substr($bin_value, $offset, $shift_num)) ^ bindec($key));
            $result = $result . str_pad($key, $shift_num, '0' ,STR_PAD_LEFT);
            $offset += $shift_num;
            $remain_length -= $shift_num;
        }

        $key = bindec(substr($bin_value, $offset)) ^ bindec(substr($key, 0, $remain_length));
        $result = $result . str_pad(decbin($key), $remain_length, '0' ,STR_PAD_LEFT);
        
        return bindec($result);
    }
    
    /*
     * 2的39次方可以保证是12位数字，40次方就到了13位数字了
     */
    private function change($key)
    {
        return  bindec(substr(decbin($key), -39));
    }

}
