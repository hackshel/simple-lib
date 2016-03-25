<?php
/**
 * @file autoload.php
 * @brief 
 *  
 **/

function get_class_for_passport($class_name) {
    if (strpos($class_name, "Passport") !== 0) {
        return false;
    }

    $cwd = dirname(__FILE__);
    $path = explode('_', $class_name);
    $end_offset  = count($path) - 1;
    foreach ($path as $key=>$value) {
        if ($key != $end_offset) {
            $path[$key] = strtolower($value);
        }
    }
    unset($path[0]);

    $real_path = $cwd . '/' . implode('/', $path).'.php';
    if (file_exists($real_path)) {
        require_once($real_path);
    }
}

spl_autoload_register('get_class_for_passport');




/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
?>
