<?
include_once 'parsers/Forwardings_Parser.php';
include_once 'parsers/Static_Hosts_Parser.php';
include_once 'parsers/DNS_Parser.php';
include_once 'config.php';


class GetHostFromCfg
{
	function get_static_host($mac)
	{
		$sp = new Static_Hosts_Parser();
		$sp->parse(file_get_contents(STATIC_HOSTS_FILE));

		$f = create_function('$h', 'return $h->mac == \''.$mac.'\';');
		$h = array_filter($sp->hosts, $f);

		if (count($h) > 1)
		{
			echo "Fatal error: found duplicated MACs in ".STATIC_HOSTS_FILE;
			exit;

		}else if(count($h) == 0) {
			 return new Host('', $mac, '');

		} else {
			return reset($h); // get the first element
		}
	}

	function read_host_from_cfg($mac)
	{
		$host = $this->get_static_host($mac);

		if (isset($_REQUEST["host"]) and $host->hostname = '')
		{
			$host->hostname = $_REQUEST["host"];
		}

		if (!isset($host->ip) or $host->ip == '') return $host;

		$forwardings_cfg = file_get_contents(FORWARDINGS_FILE);
		$forwardings = new Forwardings_Parser($forwardings_cfg);
		$host->set_forwardings($forwardings->get_rules_for($host->ip));

		$dnsp = new DNS_Parser();
		$dnsp->parse(file_get_contents(NAMED_FILE));
		$host->domains = $dnsp->get_domains_no_tld($host->ip);
		$host->tld = $dnsp->tld();

		return $host;
	}
}

?>
