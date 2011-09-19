<?
include_once 'config.php';
include_once 'parsers/SubnetsParser.php';

$sp = new SubnetsParser();
?>

<link rel="stylesheet" href="style.css">

<? include 'menu.php' ?>

<h1>DHCP subnets</h2>

<pre>
<?
$sp->parse(file_get_contents(SUBNETS_FILE));
?>
</pre>

