<?

class Forward
{
	public $public_port, $lan_port, $lan_ip;

	function __construct($public_port, $lan_port, $lan_ip)
	{
		$this->public_port = $public_port;
		$this->lan_port = $lan_port;
		$this->lan_ip = $lan_ip;
	}
}

?>
