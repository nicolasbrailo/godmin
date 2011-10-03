<?
include_once 'config.php';

include 'layout/header.php';

if (isset($_REQUEST['restart']))
{
	?>
	<div class="alert_box">You have requested to restart <?= $_REQUEST['restart'] ?> services.
	 Beware that this may affect active connections.<br/>Are you sure you want to proceed?
	<form method="post" action="system_status.php">
		<input type="submit" value="Confirm"/>
		<input type="hidden" name="confirm_restart" value="<?= $_REQUEST['restart'] ?>"/>
	</form>
	</div>
	<?
} else if (isset($_POST['confirm_restart'])) {
	switch($_POST['confirm_restart']) {
		case 'DHCP':
			OS::restart_dhcp();
			break;
		case 'DNS':
			OS::restart_dns();
			break;
		case 'Routing':
			OS::restart_nat_and_fwds();
			break;
	}
}
?>

<h1>System status</h1>
<table style="width: 500px"><tr><td>
<? include 'helpers/print_services_status.php' ?>
</td><td>
<ul>
	<li><a href="system_status.php?restart=DHCP">Restart DHCP</a></li>
	<li><a href="system_status.php?restart=DNS">Restart DNS</a></li>
	<li><a href="system_status.php?restart=Routing">Restart routing, NAT and forwards</a></li>
</ul>
</td></tr></table>

<br/><br/><br/>

<h1>Router Logs</h1>
<textarea class="smallbox">
<?= file_get_contents(ROUTER_LOG) ?>
</textarea>

<br/><br/><br/>

<h1>DHCP Logs</h1>
<textarea class="smallbox">
<? system("sudo /bin/bash ".CMD_GET_DHCP_LOG) ?>
</textarea>

<h1>IP Tables Status</h1>
<textarea class="smallbox">
<? system("sudo /bin/bash ".CMD_GET_IPTABLES_STATUS) ?>
</textarea>


<? include 'layout/footer.php' ?>
