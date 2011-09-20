<?
include_once 'GetHostFromCfg.php';
include_once 'config.php';

$hr = new GetHostFromCfg();
$host = $hr->read_host_from_cfg($_REQUEST["mac"]);
?>

<link rel="stylesheet" href="style.css">
<? include 'menu.php' ?>

<h2>Add new static DHCP host</h2>

<form method="POST" action="save_static.php">

<table class="sample">
<tr><td>Host name</td><td><input name="host" type="text" value="<?= $host->hostname ?>" /></td></tr>
<tr><td>MAC</td><td><input name="mac" type="text" value="<?= $host->mac ?>" /></td></tr>
<tr><td>IP</td><td><?= NETWORK_IP ?>.<input name="ip" type="text" size="3" maxlength="3"
															 value="<?= $host->get_network_ip() ?>" /></td></tr>

<? function print_fwd_rule($i, $rule=null) { ?>
	<tr>
		<td colspan="2">
			* Forward public port
			<input name="public_port_fwd<?= $i ?>" type="text"
									 size="5" maxlength="5" value="<?= ($rule!=null)? $rule->public_port : '' ?>" />
			to private port
			<input name="private_port_fwd<?= $i ?>" type="text"
									 size="5" maxlength="5" value="<?= ($rule!=null)? $rule->lan_port : '' ?>" />
			on this host
			<input type="button" value="X"
					  onclick="public_port_fwd<?= $i ?>.value='';
								  private_port_fwd<?= $i ?>.value=''"/>
		</td>
	</tr>
<? } ?>

<? function print_dns($i, $tld, $url=null) { ?>
	<tr>
		<td colspan="2">
			* Resolve domain 
			<input name="domain<?= $i ?>" type="text"
									 size="30" maxlength="30" value="<?= ($url!=null)? $url : '' ?>" />
			.<?= $tld ?>
			to this host.
			<input type="button" value="X" onclick="domain<?= $i ?>.value=''"/>
		</td>
	</tr>
<? } ?>

<?
$fwd_cnt = 1;
$arr = isset($host->forwardings)? $host->forwardings : array();
foreach ($arr as $rule) print_fwd_rule($fwd_cnt++, $rule);

$fwds = isset($_REQUEST["public_port_fwd"])? $_REQUEST["public_port_fwd"] : 1;
$dns = isset($_REQUEST["domains"])? $_REQUEST["domains"] : 1;
for ($i = 0; $i < $fwds; ++$i) print_fwd_rule($i+$fwd_cnt); 
?>

	<tr>
		<td colspan="2">
		<p align="right">
				<a href="add_static.php?mac=<?= $_REQUEST["mac"] ?>
								&domains=<?= $dns ?>
								&public_port_fwd=<?= $fwds+1 ?>">Add forwarding rule</a></p>
		</td>
	</tr>

<?
$dns_i = 1;
foreach($host->domains as $domain) print_dns($dns_i++, $host->tld, $domain);

$dns = isset($_REQUEST["domains"])? $_REQUEST["domains"] : 1;
for ($i = 0; $i < $dns; ++$i) print_dns($i+$dns_i, $host->tld); 
?>

	<tr>
		<td colspan="2">
		<p align="right">
				<a href="add_static.php?mac=<?= $_REQUEST["mac"] ?>
								&domains=<?= $dns+1 ?>
								&public_port_fwd=<?= $fwds ?>">Add domain</a></p>
		</td>
	</tr>

</table>

<input type="submit"/>

</form>

<h2>List of static DHCP hosts</h2>
<table class="sample" width="800px">
<tr><td>IP</td><td>MAC</td><td>Hostname</td></tr>
<? 
$sp = new StaticHostsParser();
$sp->parse(file_get_contents(STATIC_HOSTS_FILE));
foreach($sp->hosts as $lease) { ?>
	<tr>
	<td><?= $lease->ip ?></td>
	<td><?= $lease->mac ?></td>
	<td><?= $lease->hostname ?></td>
	</tr>
<? } ?>
</table>

