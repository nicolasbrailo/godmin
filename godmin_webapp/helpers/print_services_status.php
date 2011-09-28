<?
include_once 'config.php';
include_once 'OS.php';
?>

<ul>
<? foreach ($services_bin_name as $svc=>$bin) {
        if (OS::is_service_running($bin))
        {
                ?><li><img src="theme/ok.png" style="height:30px"/><?= $svc ?> is running.</li><?
        } else {
                ?><li><img src="theme/fail.png" style="height:30px"/><?= $svc ?> is not running.</li><?
        }
} ?>
