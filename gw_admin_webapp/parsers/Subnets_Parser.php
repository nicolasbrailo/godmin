<?
include_once "DHCP_Parser.php";

class Subnets_Parser extends DHCP_Parser
{
	const SUBNET_TOKEN = "subnet ";
	const NETMASK_TOKEN = "netmask";
	const RANGE_TOKEN = "range";

	function parse_block($conf, $pos, $end)
	{
		$net_ip = $this->get_by_token($conf, $pos, $end, self::SUBNET_TOKEN, self::NETMASK_TOKEN);
		$net_mask = $this->get_by_token($conf, $pos, $end, self::NETMASK_TOKEN, '{');
		$range = $this->get_by_token($conf, $pos, $end, self::RANGE_TOKEN);

		echo "Found network $net_ip/$net_mask with range $range\n";

		// $h = new Host($ip, $mac, $name, $date, $state);
		// array_push($this->leases_list, $h);
	}

	function get_block_start_tok() { return "\n".self::SUBNET_TOKEN; }

	function get_list(){ return $this->subnets; }

	var $subnets = array();
}

?>
