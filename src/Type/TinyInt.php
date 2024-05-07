<?php
namespace Cassandra\Type;

class Tinyint extends Base {
    
    /**
     * @param int|string $value
     * @throws Exception
     */
    public function __construct($value = null){
        if ($value === null)
            return;
    
        if (!is_numeric($value))
            throw new Exception('Incoming value must be type of int.');
    
        if ($value < 0 || $value > 255)
            throw new Exception('Tinyint value out of range.');
    
        $this->_value = (int) $value;
    }
    
    public static function binary($value){
        return pack('C', $value);
    }
    
    /**
     * @return int
     */
    public static function parse($binary){
        $unpacked = unpack('C', $binary);
        return $unpacked[1];
    }
}