<?php
namespace Cassandra\Type;

class Smallint extends Base {
    
    /**
     * @param int|string $value
     * @throws Exception
     */
    public function __construct($value = null){
        if ($value === null)
            return;
    
        if (!is_numeric($value))
            throw new Exception('Incoming value must be type of int.');
    
        if ($value < -32768 || $value > 32767)
            throw new Exception('Smallint value out of range.');
    
        $this->_value = (int) $value;
    }
    
    public static function binary($value){
        return pack('n', $value);
    }
    
    /**
     * @return int
     */
    public static function parse($binary){
        $unpacked = unpack('n', $binary);
        return $unpacked[1];
    }
}