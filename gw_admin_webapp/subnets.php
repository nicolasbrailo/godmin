<?
include_once 'config.php';
include_once 'parsers/Subnets_Parser.php';

$sp = new Subnets_Parser();
?>

<link rel="stylesheet" href="style.css">

<? include 'menu.php' ?>
<div id="content">
<h1>DHCP subnets</h2>

<pre>
<?
$sp->parse(file_get_contents(SUBNETS_FILE));
?>
</pre>

</div>
