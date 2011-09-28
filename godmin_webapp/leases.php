<?
include_once 'parsers/Lease_Parser.php';
include_once 'config.php';

$lp = new Lease_Parser();
$leases = $lp->parse(file_get_contents(LEASES_FILE));

include 'layout/header.php';
?>

<h1>List of Known DHCP Leases</h1>
<table class="sample">
<tr><th>IP</th><th>Lease start</th><th>MAC</th><th>Hostname</th><th>Make static</th><th>DNS Logs</th><th>Block client</th></tr>
<? foreach($leases as $lease) { ?>
	<tr>
	<td><?= $lease->ip ?></td>
	<td><?= ($lease->state != 'free')? $lease->lease_start : 'Free binding' ?></td>
	<td><?= $lease->mac ?></td>
	<td><?= $lease->hostname ?></td>
	<td><a href="add_static.php?mac=<?= $lease->mac ?>&host=<?= $lease->hostname ?>">X</a></td>
	<td><a href="dns_logs.php?filter_ip=<?= $lease->ip ?>">DNS</a></td>
	<td><a href="blocked_clients.php?block_ip=<?= $lease->ip ?>">Block</a></td>
	</tr>
<? } ?>
</table>

<? include 'layout/footer.php' ?>
