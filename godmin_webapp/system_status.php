<?
include_once 'config.php';
include 'OS.php';

function echo_server_status($svc, $svc_bin_name)
{
        if (OS::is_service_running($svc_bin_name))
        {
                ?><img src="theme/ok.png" style="height:30px"/><?= $svc ?> is running.<?
        } else {
                ?><img src="theme/fail.png" style="height:30px"/><?= $svc ?> is not running.<?
        }
}

include 'layout/header.php';
?>

<h1>System status</h1>
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
