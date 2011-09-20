<?
include_once 'GetHostFromCfg.php';
include_once 'RestartRouter.php';

if (isset($_POST["mac"]))
{
	$hr = new GetHostFromCfg();
	$host = $hr->read_host_from_cfg($_POST["mac"]);

	$sp = new Static_Hosts_Parser();
	$sp->parse(file_get_contents(STATIC_HOSTS_FILE));

	// Write the dhcp file
	$dhcpd_cfg = '';
	foreach($sp->hosts as $lease)
	{
		if ($lease->mac != $host->mac)
			$dhcpd_cfg .= $lease->dhcpd_fmt();
	}

	file_put_contents(STATIC_HOSTS_FILE, $dhcpd_cfg);

	// Write forwardings file
	$forwardings = new Forwardings_Parser(file_get_contents(FORWARDINGS_FILE));
	$forwardings->delete_rules_for($host->ip);
	file_put_contents(FORWARDINGS_FILE, $forwardings->get_cfg());

	// Write the dns cfg
	$dnsp = new DNS_Parser();
	$dnsp->parse(file_get_contents(NAMED_FILE));
	$dnsp->remove_domains_by_ip($host->ip);
	$named_cfg = $dnsp->get_print_header();
	$named_cfg .= $dnsp->get_print_domains();
	file_put_contents(NAMED_FILE, $named_cfg);

	?>
	<a href="/static_hosts.php">Saved. Continue...</a>
	<?
	restart_router();
	exit;
}

$hr = new GetHostFromCfg();
$host = $hr->read_host_from_cfg($_REQUEST["mac"]);

if ($host->ip == '') { ?>
	Error: No host found with mac <?= $_REQUEST["mac"] ?>.
	<?
	exit;
} ?>

<h2>Confirm host delete</h2>
Host <?= $host->hostname ?>.<br>
IP <?= $host->ip ?>.<br>
MAC <?= $host->mac ?>.<br>

<? if (!empty($host->forwardings)) { ?>
	Public forwards:
	<ul>
	<? foreach ($host->forwardings as $rule) { ?>
	<li>Forwarding public port <?= $rule->public_port ?> to LAN port <?= $rule->lan_port ?> on this host.</li>
	<? } ?>
	</ul>
<? } ?>

<? if (!empty($host->domains)) { ?>
	Domains:
	<ul>
	<? foreach ($host->domains as $url) { ?>
		<li><?= $url ?></li>
	<? } ?>
	</ul>
<? } ?>



<form method="POST">
<input type="hidden" name="mac" value="<?= $host->mac ?>"/>
<input type="submit" value="Confirm delete"/>
</form>

