<?php

namespace Cassandra\Type;

class Time extends Base {
    
    /**
     * @param string $timeString Format: 'HH:MM:SS'
     * @throws Exception
     */
    public function __construct($timeString = null){
        if ($timeString === null)
            return;
    
        $parsedTime = strtotime($timeString);
        if ($parsedTime === false)
            throw new Exception('Invalid time format.');
    
        $this->_value = $parsedTime;
    }
    
    public static function binary($timeString){
        $parsedTime = strtotime($timeString);
        return pack('N', $parsedTime);
    }
    
    /**
     * @return string
     */
    public static function parse($binary){
        $unpacked = unpack('N', $binary);
        return date('H:i:s', $unpacked[1]);
    }
}