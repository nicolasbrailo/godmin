<?
include_once 'config.php';

function restart_router()
{
	echo "<pre>";
	system(RESTART_CMD);
	echo "</pre>";
}

?>
