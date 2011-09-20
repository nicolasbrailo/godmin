<?php 
$currentpath = $_SERVER["PHP_SELF"];
$file = explode('/', $currentpath);
$currentpage = $file[count($file) - 1];
?>
<ul class="sandbar">
	<li id="logo"><img src="img/godmin.png" height="33" width="100" border="0" /></li>
<?php switch ($currentpage) {
    case "leases.php": ?>
	<li class="active"><a href="leases.php">Known DHCP Leases</a></li>
	<li><a href="subnets.php">Configure DHCP subnets</a></li>
	<li><a href="static_hosts.php">Static hosts</a></li>
    <li><a href="dns_logs.php">DNS Logs</a></li>
<?php break;
    case "subnets.php": ?>
	<li><a href="leases.php">Known DHCP Leases</a></li>
	<li class="active"><a href="subnets.php">Configure DHCP subnets</a></li>
	<li><a href="static_hosts.php">Static hosts</a></li>
    <li><a href="dns_logs.php">DNS Logs</a></li>
<?php break;
    case "static_hosts.php": ?>
	<li><a href="leases.php">Known DHCP Leases</a></li>
	<li><a href="subnets.php">Configure DHCP subnets</a></li>
	<li class="active"><a href="static_hosts.php">Static hosts</a></li>
    <li><a href="dns_logs.php">DNS Logs</a></li>
<?php break;
    case "dns_logs.php": ?>
	<li><a href="leases.php">Known DHCP Leases</a></li>
	<li><a href="subnets.php">Configure DHCP subnets</a></li>
	<li><a href="static_hosts.php">Static hosts</a></li>
    <li class="active"><a href="dns_logs.php">DNS Logs</a></li>
<?php break;
}
?>
</ul>
