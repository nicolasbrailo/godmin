<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Godmin &#8250; List of Static DHCP Hosts</title>
<link rel="stylesheet" href="style.css">
</head>

<?
include_once 'parsers/Static_Hosts_Parser.php';
include_once 'parsers/Forwardings_Parser.php';
include_once 'parsers/DNS_Parser.php';
include_once 'config.php';

$sp = new Static_Hosts_Parser();

$hosts = $sp->parse(file_get_contents(STATIC_HOSTS_FILE));
$forwardings = new Forwardings_Parser(file_get_contents(FORWARDINGS_FILE));

$dnsp = new DNS_Parser();
$dnsp->parse(file_get_contents(NAMED_FILE));
?>

<body>
<? include 'menu.php' ?>
<div id="content">

<h1>List of Static DHCP Hosts</h1>
<? foreach($hosts as $host) { ?>
<table class="sample" width="800px">
<tr><td>IP</td><td>MAC</td><td>Hostname</td></tr>
	<tr>
	<td><?= $host->ip ?></td>
	<td><?= $host->mac ?></td>
	<td><?= $host->hostname ?></td>
	</tr>

	<tr><td colspan="3">
	<? if($forwardings->has_rules($host->ip)) { ?>
		Public forwardings:
		<ul>
		<? foreach ($forwardings->get_rules_for($host->ip) as $rule) { ?>
		<li><?= "Public port $rule->public_port will be forwarded to port $rule->lan_port on this host" ?></li>
		<? } ?>
		</ul>
	<? }else{ ?>
		No public forwardings found
	<? } ?>
	</td></tr>

	<tr><td colspan="3">
	<? $domains = $dnsp->get_domains($host->ip) ?>
	<? if (count($domains) == 0) { ?>
		No known domains.
	<? }else{ ?>
		Known domain names:
		<ul>
		<? foreach($domains as $dm) { ?>
			<li><?= $dm ?></li>
		<? } ?>
		</ul>
	<? } ?>
	</td></tr>

	<tr><td colspan="3">
		<a href="add_static.php?mac=<?= $host->mac ?>">Edit</a> |  
		<a href="del_static.php?mac=<?= $host->mac ?>">Delete</a>
	</tr>
</table>
<br>
<? } ?>

</div>
</body>
</html>