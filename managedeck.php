<?php session_start(); ?>
<!doctype html>
<html>
<head>
<title>Deckbuilder</title>
</head>
<body>
<a href='index.php'> &lt;&lt; Go home </a> <br />
<a href='index.php'> &lt;&lt; Go to search </a> <br />
<?php
$con = mysql_connect("localhost", "root", "");
if (!$con)
  {
  die('Error: ' . mysql_error());
  }
$db_selected = mysql_select_db("deckbuilder", $con);

$result = mysql_query("SELECT * from `ndecklist`");

if (!$result) die('Error: '.mysql_error());

if (isset($_POST["createdeck"])) {
	if (!mysql_query("INSERT INTO `ndecklist` (name, format) VALUES ('".mysql_real_escape_string($_POST["name"])."','".mysql_real_escape_string($_POST["format"])."')"))
		die('Error: '.mysql_error());
	echo "Deck Created<br/>";
}
if (isset($_POST["updatedeck"])) {
	if (!isset($_SESSION["selected"])) die ("No deck selected!");
	foreach (explode(",",$_POST["changed"]) as $var) {
		if (!mysql_query("DELETE FROM `ndecks` WHERE deckid = ".mysql_real_escape_string($_SESSION["selected"])." AND card = ".mysql_real_escape_string($var)))
			die('Error: '.mysql_error());
		if ($_POST[$var]==0) continue;
		if (!mysql_query("INSERT INTO `ndecks` (deckid, card, quantity) VALUES ('1','".mysql_real_escape_string($var)."','".mysql_real_escape_string($_POST[$var])."')"))
			die('Error: '.mysql_error());
	}
	echo "Deck updated<br/>";
}

mysql_free_result($result);

$results = mysql_query("SELECT
ncards.nname, ndecks.quantity, ndecks.main, ndecklist.name, ndecklist.format
FROM `ndecks`
INNER JOIN ndecklist ON ndecks.deckid=ndecklist.id
INNER JOIN ncards ON ncards.nid = ndecks.card
WHERE ndecks.deckid = ".mysql_real_escape_string($_SESSION["selected"])."
ORDER BY ndecks.main ASC, ncards.ntype ASC, ncards.nconverted_manacost ASC");
if (!$results) die("Error: ".mysql_error());
$found = false;
while($row = mysql_fetch_assoc($results))	{
	if (!$found) echo "<b>{$row[name]}</b><br/><table border=1><tr><th>Card</th><th>Quantity</th></tr>";
	$found = true;
	echo "<tr><td>{$row[nname]}</td><td>{$row[quantity]}</td></tr>";
}
if (!$found) echo "Wut";
else echo "</table>";

mysql_close($con);
?>
</body>
</html>