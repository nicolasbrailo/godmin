<?
include_once 'config.php';
include_once 'OS.php';


if (isset($_POST["refresh"]))
{
        echo "Generating new proxy reports... it will take a while, please wait.</br>";
        OS::generate_proxy_report();
        echo "Done! <a href='proxy_logs.php'>Continue</a>.</br>";
        exit;
}


include 'layout/header.php';
?>
<h1>Proxy logs</h1>
<div id="lol"><img src="theme/thought_police.png" height="80" alt="Legum servi sumus ut liberi esse possimus" /></div>

<form method="post">
<b>Beware, generating proxy reports implies processing a couple hundred megabytes, and it's a lengthy process... you should do this only if you have a low network load (and don't even think of doing this unless you have a multi core box for Godmin, otherwise your whole network will grind to a halt).</b><br/><br/>
<input type="submit" name="refresh" value="Refresh proxy reports"/>
</form>

<hr/><br/>

<iframe src="/<?= PROXY_REPORT_DIR ?>"
        width="100%" height="400" scrolling="auto" frameborder="1" transparency>
        <p>Your browser doesn't support iframes and I'm too lazy to display the logs any other way.</p>
</iframe>

<? include 'layout/footer.php' ?>

