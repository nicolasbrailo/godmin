<?
include_once 'parsers/Lease_Parser.php';
include_once 'config.php';

$lp = new Lease_Parser();
$leases = $lp->parse(file_get_contents(LEASES_FILE));
?>

<link rel="stylesheet" href="style.css">

<? include 'menu.php' ?>
<div id="content">

<h1>List of known DHCP leases</h1>
<table class="sample">
<tr><th>IP</th><th>Lease start</th><th>MAC</th><th>Hostname</th><th>Make static</th><th>DNS Logs</th></tr>
<? foreach($leases as $lease) { ?>
	<tr>
	<td><?= $lease->ip ?></td>
	<td><?= ($lease->state != 'free')? $lease->lease_start : 'Free binding' ?></td>
	<td><?= $lease->mac ?></td>
	<td><?= $lease->hostname ?></td>
	<td><a href="add_static.php?mac=<?= $lease->mac ?>&host=<?= $lease->hostname ?>">X</a></td>
	<td><a href="dns_logs.php?filter_ip=<?= $lease->ip ?>">DNS</a></td>
	</tr>
<? } ?>
</table>

</div>
