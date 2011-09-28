<?
include_once 'config.php';
include_once 'parsers/Blocked_Clients_Parser.php';

$bcp = new Blocked_Clients_Parser();
$bcp->parse(file_get_contents(BLOCKED_CLIENTS_FILE));

if (isset($_POST["ip"]))
{
	$bcp->block_client($_POST["ip"]);
	$bcp->write_config(BLOCKED_CLIENTS_FILE);
}

if (isset($_REQUEST["unblock"]))
{
	$bcp->unblock_client($_REQUEST["unblock"]);
	$bcp->write_config(BLOCKED_CLIENTS_FILE);
}

include 'layout/header.php';
?>

<? if (isset($_REQUEST['block_ip'])) { ?>
	<h1>Block client</h1>
	<p>
	Are you sure you wish to block client <?= $_REQUEST['block_ip'] ?>?<br/><br/>
	</p>

	<br/><br/>

	<form method="post" action="blocked_clients.php">
		<input type="hidden" name="ip" value="<?= $_REQUEST['block_ip'] ?>"/>
		<input type="submit" value="Confirm"/>
	</form>

	<br/><hr/><br/>
<? } ?>


<h1>List of blocked clients</h1>
<? if (0 == count($bcp->get_list())) { ?>
	No clients are blocked at the moment
<? } else { ?>
	<ul>
	<? foreach($bcp->get_list() as $ip) { ?>
		<li><?= $ip ?> (<a href="blocked_clients.php?unblock=<?= $ip ?>">Unblock</a>)</li>
	<? } ?>
	</ul>
<? } ?>

<p class='alert_box'>All the connections for the clients on this list will be forwarded to a captive portal, meaning they will be have all connection attempts to the 'outer world' blocked, with the exception of HTTP requests on port 80 which will be forwarded to a special page on <?= CAPTIVE_PORTAL_ADDR ?>.<br/><br/>

Please note that if dynamic clients are listead here (instead of only static ones) they might be able to avoid the captive portal simply by renewing their IP; this functionality is intended as a way to send a message or a warning to a user, not as a security meassure.</p>

<? include 'layout/footer.php' ?>
