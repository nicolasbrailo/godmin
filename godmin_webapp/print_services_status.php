<?
include_once 'config.php';
include_once 'OS.php';

function echo_server_status($svc, $svc_bin_name)
{
        if (OS::is_service_running($svc_bin_name))
        {
                ?><img src="theme/ok.png" style="height:30px"/><?= $svc ?> is running.<?
        } else {
                ?><img src="theme/fail.png" style="height:30px"/><?= $svc ?> is not running.<?
        }
}
?>
