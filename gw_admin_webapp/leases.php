<?
include_once 'parsers/LeaseParser.php';
include_once 'config.php';

$lp = new LeaseParser();
$leases = $lp->parse(file_get_contents(LEASES_FILE));
?>

<link rel="stylesheet" href="style.css">

<? include 'menu.php' ?>

<h1>List of known DHCP leases</h2>
<table class="sample" width="900px">
<tr><td>IP</td><td>Lease start</td><td>MAC</td><td>Hostname</td><td>Make static</td><td>DNS Logs</td></tr>
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

