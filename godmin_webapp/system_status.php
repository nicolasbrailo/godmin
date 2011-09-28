<?
include_once 'config.php';
include 'layout/header.php';
?>

<h1>System status</h1>
<? include_once 'print_services_status.php' ?>
<ul>
<? foreach ($services_bin_name as $svc=>$bin) { ?>
        <li><? echo_server_status($svc, $bin) ?></li>
<? } ?>
</ul>

<br/><br/>

<h1>Router Logs</h1>
<textarea class="smallbox">
<?= file_get_contents(ROUTER_LOG) ?>
</textarea>

<br/><br/><br/>

<h1>DHCP Logs</h1>
<textarea class="smallbox">
<? system("sudo /bin/bash ".CMD_GET_DHCP_LOG) ?>
</textarea>


<? include 'layout/footer.php' ?>
