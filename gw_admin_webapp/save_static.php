<?
include_once 'parsers/DNS_Parser.php';
include_once 'GetHostFromCfg.php';
include_once 'RestartRouter.php';

if (!isset($_POST["mac"]))
{
	echo "<a href='static_hosts.php'>Error. Continue...</a>";
	exit;
}

$hr = new GetHostFromCfg();
$host = $hr->read_host_from_cfg($_POST["mac"]);

// Get new hostname and IP from POST
$host->old_ip = $host->ip;
if (isset($_POST["ip"])) $host->ip = NETWORK_IP . '.' . $_POST["ip"];
if (isset($_POST["host"])) $host->hostname = $_POST["host"];

// Read forwardings from POST
$host->forwardings = array();
$i = 1;
$a = 'public_port_fwd';
$b = 'private_port_fwd';
while (isset($_POST[$a.$i]) and isset($_POST[$b.$i]))
{
	$pub = $_POST[$a.$i];
	$priv = $_POST[$b.$i];
	if (is_numeric($pub) and is_numeric($priv))
	{
		$host->add_forwarding(new Forward($pub, $priv, $host->ip));
	}

	++$i;
}

// Read DNS domains from POST
$host->domains = array();
$i = 1;
$a = 'domain';
while (isset($_POST[$a.$i]))
{
	$domain = trim($_POST[$a.$i]);
	if ($domain != '') array_push($host->domains, $domain);
	++$i;
}

// Read the static hosts config, validate the changes
$sp = new Static_Hosts_Parser();
$sp->parse(file_get_contents(STATIC_HOSTS_FILE));
$sp->add_or_modify_host($host);

$h = $sp->get_ip_collision($host);
if ($h !== null)
{
	echo "Error: $h->ip collides with the IP for $h->hostname.";
	echo "<a href='/static_hosts.php'>Continue...</a>";
	exit;
}

$h = $sp->get_hostname_collision($host);
if ($h !== null)
{
	echo "Error: $h->hostname collides with the hostname for $h->ip.";
	echo "<a href='/static_hosts.php'>Continue...</a>";
	exit;
}


// Read the forwardings, check for errors
$forwardings = new Forwardings_Parser(file_get_contents(FORWARDINGS_FILE));
$forwardings->delete_rules_for($host->ip);
$forwardings->delete_rules_for($host->old_ip);
foreach ($host->forwardings as $f)
{
	if (!$forwardings->add($f))
	{
		$dup = $forwardings->get_duplicated_rule($f);
		echo "Error adding rule $f->public_port -> $f->lan_ip:$f->lan_port. ";
		echo "Rule colides with $dup->public_port -> $dup->lan_ip:$dup->lan_port.";
		exit;
	}
}


$dnsp = new DNS_Parser();
$dnsp->parse(file_get_contents(NAMED_FILE));
$dnsp->remove_domains_by_ip($host->old_ip);
$dnsp->remove_domains_by_ip($host->ip);

foreach($host->domains as $url)
{
	if (!$dnsp->add_url($url, $host->ip))
	{
		echo "Error: $url already has an assigned host";
		exit;
	}
}

// If we got to this point, both forwardings and static config are OK

// Write the dhcp file
$dhcpd_cfg = '';
foreach($sp->hosts as $lease)
{
	$dhcpd_cfg .= $lease->dhcpd_fmt();
}
file_put_contents(STATIC_HOSTS_FILE, $dhcpd_cfg);

// Write the forwardings cfg
file_put_contents(FORWARDINGS_FILE, $forwardings->get_cfg());


// Write the dns cfg
$named_cfg = $dnsp->get_print_header();
$named_cfg .= $dnsp->get_print_domains();
file_put_contents(NAMED_FILE, $named_cfg);


echo '<a href="/static_hosts.php">Saved. Continue...</a>';
restart_router();

?>
