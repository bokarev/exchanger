<?php
# WMXILogger class
if(!class_exists('WMXILogger')){
class WMXILogger {
    public static function Append($message, $detailed = false) {
        if (!defined('WMXI_LOG')) {
            return false;
        }
        $trace = debug_backtrace(true);
        $path = '';
        foreach($trace as $info) {
            $args = '';
            foreach($info['args'] as $arg) {
                if (is_scalar($arg)) {
                    $arg = "'$arg'";
                }
                if (is_array($arg)) {
                    $arg = "unserialize('" . serialize($arg) . "')";
                }
                $args.= "$arg, ";
            }
            $args = substr($args, 0, -2);
            $path = basename($info['file']) . ' : ' . $info['line'] . "\t[ " . @$info['class'] . @$info['type'] . $info['function'] . "($args); ]" . "\n" . $path;
            #break;
            
        }
        file_put_contents(WMXI_LOG, $path . "\n", FILE_APPEND | LOCK_EX);
        if ($detailed) {
            file_put_contents(WMXI_LOG, print_r($trace, true), FILE_APPEND | LOCK_EX);
        }
    }
}
}
?>