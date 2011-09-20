<?
include_once "DHCP_Parser.php";
include_once "models/Host.php";

class Lease_Parser extends DHCP_Parser
{
	const LEASE_TOKEN = "lease ";
	const STARTS_TOKEN = "starts ";
	const MAC_TOKEN = "ethernet ";
	const HOSTNAME_TOKEN = "client-hostname";
	const BINDING_STATE = "binding state";

	function parse_block($leases, $pos, $end)
	{
		$ip = $this->get_by_token($leases, $pos, $end, 'lease', '{');
		$mac = $this->get_by_token($leases, $pos, $end, self::MAC_TOKEN);
		$date = $this->get_by_token($leases, $pos, $end, self::STARTS_TOKEN);
		$name = $this->get_by_token($leases, $pos, $end, self::HOSTNAME_TOKEN);
		$state = $this->get_by_token($leases, $pos, $end, self::BINDING_STATE);

		$h = new Host($ip, $mac, $name, $date, $state);
		array_push($this->leases_list, $h);
	}

	function get_block_start_tok() { return "\n".self::LEASE_TOKEN; }

	function get_list(){ return $this->leases_list; }

	var $leases_list = array();
}

?>
