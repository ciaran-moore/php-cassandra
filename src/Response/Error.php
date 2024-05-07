<?php
namespace Cassandra\Response;
use Cassandra\Protocol\Frame;

class Error extends Response {
	const SERVER_ERROR = 0x0000;
	const PROTOCOL_ERROR = 0x000A;
	const BAD_CREDENTIALS = 0x0100;
	const UNAVAILABLE_EXCEPTION = 0x1000;
	const OVERLOADED = 0x1001;
	const IS_BOOTSTRAPPING = 0x1002;
	const TRUNCATE_ERROR = 0x1003;
	const WRITE_TIMEOUT = 0x1100;
	const READ_TIMEOUT = 0x1200;
    const READ_FAILURE = 0x1300;
    const FUNCTION_FAILURE = 0x1400;
    const WRITE_FAILURE = 0x1500;
	const SYNTAX_ERROR = 0x2000;
	const UNAUTHORIZED = 0x2100;
	const INVALID = 0x2200;
	const CONFIG_ERROR = 0x2300;
	const ALREADY_EXIST = 0x2400;
	const UNPREPARED = 0x2500;
	
	/**
	 * Indicates an error processing a request. The body of the message will be an
	 * error code ([int]) followed by a [string] error message. Then, depending on
	 * the exception, more content may follow. The error codes are defined in
	 * Section 7, along with their additional content if any.
	 *
	 * @return array
	 */
    public function getData() {
        $this->_stream->offset(0);
        $data = [];
        $data['code'] = $this->_stream->readInt();

        switch($data['code']){
            case self::SERVER_ERROR:
                $data['message'] = "Server error - Something unexpected happened. This indicates a server-side bug: " . $this->_stream->readString();
                break;

            case self::PROTOCOL_ERROR:
                $data['message'] = "Protocol error - Some client message triggered a protocol violation (for instance a QUERY message is sent before a STARTUP one has been sent): " . $this->_stream->readString();
                break;

            case self::BAD_CREDENTIALS:
                $data['message'] = "Authentication error - Authentication was required and failed. The possible reason for failing depends on the authenticator in use, which may or may not include more detail in the accompanying error message: " . $this->_stream->readString();
                break;
            // ERROR message body: <cl><required><alive>
            case self::UNAVAILABLE_EXCEPTION:
                $data['message'] = "Unavailable exception. Error data: " . var_export([
                        'error'=>$this->_stream->readString(),
                        'consistency' => $this->_stream->readShort(),
                        'node' => $this->_stream->readInt(),
                        'replica' => $this->_stream->readInt()
                    ], true);
                break;

            case self::OVERLOADED:
                $data['message'] = "Overloaded - the request cannot be processed because the coordinator node is overloaded: " . $this->_stream->readString();
                break;

            case self::IS_BOOTSTRAPPING:
                $data['message'] = "Is_bootstrapping - the request was a read request but the coordinator node is bootstrapping: " . $this->_stream->readString();
                break;

            case self::TRUNCATE_ERROR:
                $data['message'] = "Truncate_error - Error during a truncation error: " . $this->_stream->readString();
                break;
            // ERROR message body:  <cl><received><blockfor><writeType>
            case self::WRITE_TIMEOUT:
                $data['message'] = "Write_timeout - Timeout exception during a write request. Error data: " . var_export([
                        'error'=>$this->_stream->readString(),
                        'consistency' => $this->_stream->readShort(),
                        'node' => $this->_stream->readInt(),
                        'replica' => $this->_stream->readInt(),
                        'writeType' => $this->_stream->readString()
                    ], true);
                break;
            // ERROR message body:  <cl><received><blockfor><data_present>
            case self::READ_TIMEOUT:
                $data['message'] = "Read_timeout - Timeout exception during a read request. Error data: " . var_export([
                    'error'=>$this->_stream->readString(),
                    'consistency' => $this->_stream->readShort(),
                    'node' => $this->_stream->readInt(),
                    'replica' => $this->_stream->readInt(),
                    'dataPresent' => $this->_stream->readChar()
                    ], true);
                break;
            // ERROR message body: <cl><received><blockfor><numfailures><data_present>
            case self::READ_FAILURE:
                $data['message'] = "Read_failure - A non-timeout exception during a read request. Error data: " . var_export([
                    'error'=>$this->_stream->readString(),
                    'consistency' => $this->_stream->readShort(),
                    'node' => $this->_stream->readInt(),
                    'replica' => $this->_stream->readInt(),
                    'numfailures' => $this->_stream->readInt(),
                    'dataPresent' => $this->_stream->readChar()
                    ], true);
                break;
            // ERROR message body: <keyspace><function><arg_types>
            case self::FUNCTION_FAILURE:
                $data['message'] = "Function_failure - A (user defined) function failed during execution. Error data: " . var_export([
                    'error'=>$this->_stream->readString(),
                    'keyspace' => $this->_stream->readString(),
                    'function' => $this->_stream->readString(),
                    'arg_types' => $this->_stream->readString(),
                    ], true);
                break;
            // ERROR message body: <cl><received><blockfor><numfailures><write_type>
            case self::WRITE_FAILURE:
                $data['message'] = "Write_failure - A non-timeout exception during a write request. Error data: " . var_export([
                    'error'=>$this->_stream->readString(),
                    'consistency' => $this->_stream->readShort(),
                    'node' => $this->_stream->readInt(),
                    'replica' => $this->_stream->readInt(),
                    'writeType' => $this->_stream->readString()
                    ], true);
                break;

            case self::SYNTAX_ERROR:
                $data['message'] = "Syntax_error - The submitted query has a syntax error: " . $this->_stream->readString();
                break;

            case self::UNAUTHORIZED:
                $data['message'] = "Unauthorized - The logged user doesn't have the right to perform the query: " . $this->_stream->readString();
                break;

            case self::INVALID:
                $data['message'] = "Invalid - The query is syntactically correct but invalid: " . $this->_stream->readString();
                break;

            case self::CONFIG_ERROR:
                $data['message'] = "Config_error - The query is invalid because of some configuration issue: " . $this->_stream->readString();
                break;
            // ERROR message body: <ks><table>
            case self::ALREADY_EXIST:
                $data['message'] = "Already_exists - The query attempted to create a keyspace or a table that was already existing: " . var_export([
                    'error'=>$this->_stream->readString(),
                    'keyspace' => $this->_stream->readString(),
                    'table' => $this->_stream->readString()
                    ], true);
                break;

            case self::UNPREPARED:
                $data['message'] = "Unprepared - Can be thrown while a prepared statement tries to be executed if the provided prepared statement ID is not known by this host: " . $this->_stream->readShort();
                break;

            default:
                $data['message'] = 'Unknown error';
        }
        
        return $data;
    }
	
	/**
	 * 
	 * @return Exception
	 */
	public function getException(){
		$data = $this->getData();
		return new Exception($data['message'], $data['code']);
	}
}
