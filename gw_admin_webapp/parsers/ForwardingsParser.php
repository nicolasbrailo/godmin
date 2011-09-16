<?
include_once 'models/Forward.php';

class ForwardingsParser
{
	const PUBLIC_FWD_RULE = 'iptables -t nat -A PREROUTING -i eth0 -p tcp -j DNAT';

	function get_val($rule, $tok)
	{
		$max = strlen($rule);
		$i = strpos($rule, $tok)+strlen($tok);

		// Skip blanks
		while ($i < $max and trim($rule[$i]) == '') ++$i;

		// Copy until we get blanks again
		$val = '';
		while ($i < $max and trim($rule[$i]) != '') $val .= $rule[$i++];

		return $val;
	}

	function __construct($forwardings)
	{
		$fwds = explode("\n", $forwardings);
		$f = create_function('$l', 'return (strpos($l, \''.self::PUBLIC_FWD_RULE.'\') !== false);');
		$fwds = array_filter($fwds, $f);

		foreach($fwds as $rule)
		{
			$lan_dir = explode(':', $this->get_val($rule, '--to'));
			$lan_ip = $lan_dir[0];
			$lan_port = $lan_dir[1];
			$pub_port = $this->get_val($rule, '--dport');

			array_push($this->rules, new Forward($pub_port, $lan_port, $lan_ip));
		}
	}

	function delete_rules_for($ip)
	{
		$add = array();
		foreach ($this->rules as $rule)
		{
			if ($rule->lan_ip != $ip) array_push($add, $rule);
		}
		$this->rules = $add;
	}

	function get_rules_for($ip)
	{
		$f = create_function('$l', 'return ($l->lan_ip == \''.$ip.'\');');
		return array_filter($this->rules, $f);
	}

	function has_rules($ip)
	{
		$t = $this->get_rules_for($ip);
		return !empty($t);
	}

	function get_duplicated_rule($f)
	{
		foreach ($this->rules as $rule)
		{
			if ($rule->public_port == $f->public_port) return $rule;
			if ($rule->lan_port == $f->lan_port
						and $rule->lan_ip == $f->lan_ip) return $rule;
		}

		return null;
	}

	function add($f)
	{
		if ($this->get_duplicated_rule($f) !== null) return false;
		array_push($this->rules, $f);
		return true;
	}

	function get_cfg()
	{
		$str = '';
		foreach ($this->rules as $rule)
		{
			$str .= self::PUBLIC_FWD_RULE
						. ' --dport ' . $rule->public_port
						. ' --to ' . $rule->lan_ip . ':' . $rule->lan_port . "\n";

			$str .= '#iptables -A INPUT -i eth0 -p tcp -m state --state NEW -j DNAT '
						. ' --dport ' . $rule->public_port
						. ' --to ' . $rule->lan_ip . ':' . $rule->lan_port . "\n";
		}

		return $str;
	}

	public $rules = array();
}

?>
