<?php
namespace Cassandra\Request;
use Cassandra\Protocol\Frame;

class Startup extends Request{
	
	protected $opcode = Frame::OPCODE_STARTUP;
	
	/**
	 * 
	 * @var array
	 */
	protected $_options = [];
	
	/**
	 * STARTUP
	 *
	 * Initialize the connection. The server will respond by either a READY message
	 * (in which case the connection is ready for queries) or an AUTHENTICATE message
	 * (in which case credentials will need to be provided using CREDENTIALS).
	 *
	 * This must be the first message of the connection, except for OPTIONS that can
	 * be sent before to find out the options supported by the server. Once the
	 * connection has been initialized, a client should not send any more STARTUP
	 * message.
	 *
	 * Possible options are:
	 * - "CQL_VERSION": the version of CQL to use. This option is mandatory and
	 * currenty, the only version supported is "3.0.0". Note that this is
	 * different from the protocol version.
	 * - "COMPRESSION": the compression algorithm to use for frames (See section 5).
	 * This is optional, if not specified no compression will be used.
     * - "NO_COMPACT": whether or not connection has to be established in compatibility
     * mode. This mode will make all Thrift and Compact Tables to be exposed as if
     * they were CQL Tables. This is optional; if not specified, the option will
     * not be used.
     * - "THROW_ON_OVERLOAD": In case of server overloaded with too many requests, by 
     * default the server puts back pressure on the client connection. Instead, the server 
     * can send an OverloadedException error message back to the client if this option is 
     * set to true.
	 *
	 * @param array $options
	 */
	public function __construct(array $options = []) {
		$this->_options = $options;
	}
	
	public function getBody(){
		$body = pack('n', count($this->_options));
		foreach ($this->_options as $name => $value) {
			$body .= pack('n', strlen($name)) . $name;
			$body .= pack('n', strlen($value)) . $value;
		}
		return $body;
	}
}
