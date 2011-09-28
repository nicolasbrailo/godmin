<?
include_once 'parsers/DHCP_Parser.php';

class Blocked_Clients_Parser extends DHCP_Parser
{
	function parse_block($str, $pos, $max)
	{
		$ip = $this->get_by_token($str, $pos, $max, '-s ', '-j');
		array_push($this->ips, $ip);
	}

	function block_client($ip)
	{
		array_push($this->ips, $ip);
		$this->ips = array_unique($this->ips);
	}

	function unblock_client($ip)
	{
		$f = create_function('$a', 'return $a != "'.$ip.'";');
		$this->ips = array_filter($this->ips, $f);
	}

	function write_config($fname)
	{
		$cfg = '';
		foreach ($this->ips as $ip)
		{
			$cmd = "iptables -tnat -A PREROUTING -p tcp -s $ip -jDNAT ";
			$cmd .= "--to-destination ".CAPTIVE_PORTAL_ADDR."\n";
			$cfg .= $cmd;
		}

		file_put_contents($fname, $cfg, LOCK_EX);
	}

	function get_block_start_tok() { return 'iptables -tnat -A PREROUTING'; }
	function get_block_end_tok() { return "\n"; }
	function get_list() { return $this->ips; }

	private $ips = array();
}

?>
