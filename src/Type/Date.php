<?php

namespace Cassandra\Type;

class Date extends Base {
    
    /**
     * @param string $dateString Format: 'YYYY-MM-DD'
     * @throws Exception
     */
    public function __construct($dateString = null){
        if ($dateString === null)
            return;
    
        $parsedDate = strtotime($dateString);
        if ($parsedDate === false)
            throw new Exception('Invalid date format.');
    
        $this->_value = $parsedDate;
    }
    
    public static function binary($dateString){
        $parsedDate = strtotime($dateString);
        return pack('N', $parsedDate);
    }
    
    /**
     * @return string
     */
    public static function parse($binary){
        $unpacked = unpack('N', $binary);
        return date('Y-m-d', $unpacked[1]);
    }
}
Explanation:

