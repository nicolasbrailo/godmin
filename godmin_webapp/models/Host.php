<?
include_once 'config.php';

class Host
{
	public $ip, $lease_start, $mac, $hostname, $state, $forwardings;
	public $old_ip, $tld;
	public $domains = array();

	function __construct($ip, $mac, $hostname, $lease_start='', $state='')
	{
		$this->ip = $ip;
		$this->lease_start = $lease_start;
		$this->mac = $mac;
		$this->hostname = $hostname;
		$this->state = $state;
	}

	function set_forwardings($f)
	{
		$this->forwardings = $f;
	}

	function add_forwarding($f)
	{
		if ($this->forwardings == null) $this->forwardings = array();
		foreach ($this->forwardings as $rule)
		{
			if ($rule->public_port == $f->public_port) return;
			if ($rule->lan_port == $f->lan_port) return;
		}

		array_push($this->forwardings, $f);
	}

	function get_network_ip()
	{
		$p = strlen(NETWORK_IP) + 1;
		return substr($this->ip, $p, 3);
	}

	function dhcpd_fmt()
	{
		return "host $this->hostname {\n" .
			"  hardware ethernet $this->mac;\n".
			"  fixed-address $this->ip;\n".
			"}\n";
	}

}

?>
