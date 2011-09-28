<?
include_once 'OS.php';
include_once 'config.php';

foreach ($services_bin_name as $svc=>$bin) {
        if (! OS::is_service_running($bin))
	{

?>
<div id="fatal_error_message" onClick="this.style.display='none';">
	<h1>Fatal error: Service down</h1>
	<p align="right">Click to continue...</p>

	<p>
		If you are seeing this message it means that a required service in the router is down.<br/>
		Possible solutions:
		<ul>
			<li>Restarting each service in Godmin (Check the 'System status' page)</li>
			<li>Logging in to the router via ssh and troubleshoot there</li>
			<li>Reboot the machine, Windows style</li>
		</ul>
	</p>

	<hr/>

	<? include 'helpers/print_services_status.php' ?>

	<div style="position: relative; left: 493px; top: -200px">
	<img height="300px" src="theme/fatal_error.jpg"/>
	</div>
</div>

<?
		break;
	}
}
?>
