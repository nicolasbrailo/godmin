<?
class DNS_Parser
{
	function parse($dns_conf)
	{
		$f1 = create_function('$l', 'return (strpos($l, "'.NETWORK_IP.'") !== false);');
		$f2 = create_function('$l', 'return (strpos($l, "'.NETWORK_IP.'") === false);');

		$conf = explode("\n", $dns_conf);
		$this->header = array_filter($conf, $f2);
		$this->domains = $this->parse_domains(array_filter($conf, $f1));
		$this->tld = $this->get_tld($this->header);
	}

	function remove_domains_by_ip($ip)
	{
		$l = array();
		foreach($this->domains as $d)
		{
			if ($d[0] != $ip) array_push($l, $d);
		}

		$this->domains = $l;
	}

	function add_url($url, $ip)
	{
		foreach ($this->domains as $d)
			if ($d[0] == $url) return false;

		array_push($this->domains, array($ip, $url));
		return true;
	}

	function get_domains_no_tld($ip)
	{
		$l = array();
		foreach ($this->domains as $d)
			if ($d[0] == $ip) array_push($l, $d[1]);

		return $l;
	}

	function get_domains($ip)
	{
		$l = array();
		foreach ($this->domains as $d)
			if ($d[0] == $ip) array_push($l, $d[1] . '.' . $this->tld);

		return $l;
	}

	function parse_domains($cfg)
	{
		$domains = array();
		foreach($cfg as $l)
		{
			$v = explode(" ", $l);
			$domain = trim(reset($v));
			$ip = trim(end($v));
			$d = array($ip, $domain);
			array_push($domains, $d);
		}

		return $domains;
	}

	function get_tld($cfg)
	{
		$f1 = create_function('$l', 'return (strpos($l, "SOA") !== false);');
		$tld_line = reset(array_filter($cfg, $f1));
		$tld = reset(explode(" ", trim($tld_line)));
		$p = strpos($tld, '.');
		return substr($tld, 0, $p);
	}

	function get_print_domains()
	{
		$s = '';
		foreach($this->domains as $l)
		{
			$ip = $l[0];
			$domain = $l[1];
			$s .= "$domain    IN      A       $ip\n";
		}

		return $s;
	}

	function get_print_header()
	{
		$s = '';
		foreach($this->header as $l) $s .= $l . "\n";
		return $s;
	}

	function tld(){ return $this->tld; }

	private $domains = array();
	private $header = array();
	private $tld = '';
}
?>
