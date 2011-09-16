<?
include_once "DHCP_Parser.php";
include_once "models/Host.php";

class StaticHostsParser extends DHCP_Parser
{
	const HOST_TOKEN = "host ";
	const MAC_TOKEN = "ethernet ";
	const IP_TOKEN = "fixed-address ";

	function parse_block($leases, $pos, $end)
	{
		$name = $this->get_by_token($leases, $pos, $end, self::HOST_TOKEN, '{');
		$mac = $this->get_by_token($leases, $pos, $end, self::MAC_TOKEN);
		$ip = $this->get_by_token($leases, $pos, $end, self::IP_TOKEN);
		$h = new Host($ip, $mac, $name, 'Never expires', 'static');

		array_push($this->hosts, $h);
	}

	function get_block_start_tok() { return self::HOST_TOKEN; }

	function get_list(){ return $this->hosts; }

	function get_ip_collision($host)
	{
		foreach ($this->hosts as $h)
		{
			if ($h->ip == $host->ip and $h->mac != $host->mac)
				return $h;
		}

		return null;
	}

	function get_hostname_collision($host)
	{
		foreach ($this->hosts as $h)
		{
			if ($h->hostname == $host->hostname and $h->mac != $host->mac)
				return $h;
		}

		return null;
	}

	/**
	 * Will modify a host, searching by MAC. If the host is not found, it will
	 * be added.
	 */
	function add_or_modify_host($host)
	{
		foreach($this->hosts as $lease)
		{
			if ($lease->mac == $host->mac)
			{
				$lease->ip = $host->ip;
				$lease->hostname = $host->hostname;
				return;
			}
		}

		// It's not already on the list, push it
		array_push($this->hosts, $host);
	}

	var $hosts = array();
}

?>
