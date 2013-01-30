<?php session_start();  ?>
<html>
<head>
<script type="text/javascript">
function createdeck() {
	var elem = document.getElementById(0);
	elem.innerHTML="<form action='managedeck.php' method='POST'> \
	<input type='hidden' name='createdeck' value='true'> \
	Name: <input type='textbox' name='name'> <input type='submit' name='submit'> <br /> \
	Format: <select name='format'> <option value='Standard'>Standard</option>\
	<option value='Modern'>Modern</option>\
	<option value='Legacy'>Legacy</option>\
	<option value='Vintage'>Vintage</option>\
	<option value='Block'>Block constructed</option>\
	<option value='EDH'>EDH/Commander</option>\
	<option value='Multiplayer'>Multiplayer</option>\
	<option value='Casual'>Casual</option> </select> </form>";
}
</script>
<title>
DeckBuilder
</title>
</head>

<body>

<form action="search.php" method="GET">
<input type="textbox" name="query"/>
<input type="submit" value="Submit" /> <br />
<input type="checkbox" name="where[]" value="Text" checked="checked" /> Text
<input type="checkbox" name="where[]" value="Name" checked="checked" /> Name
<input type="checkbox" name="where[]" value="Type" /> Type
</form>

<a href="managedeck.php">View decks</a>
<?php 
if (isset($_POST["selected"]))
	$_SESSION["selected"] = intval($_POST["selected"]);

$con = mysql_connect("localhost", "root", "");
if (!$con)
  {
  die('Error: ' . mysql_error());
  }
$db_selected = mysql_select_db("deckbuilder", $con);

$result = mysql_query("SELECT * from `ndecklist`");

if (!$result) die('Error: '.mysql_error());
$found = false;
//<form method='POST' action='index.php'>
while($row = mysql_fetch_array($result)) {
	if (!$found) echo "<form action='index.php' method='POST'><br /><table border=1><tr><th>Deck Name</th><th>ID</th><th>Format</th><th>Selected</th></tr>";
	$found = true;
	if ($_SESSION["selected"] == $row["id"]) $default = "checked='checked'";
	else $default = "";
	echo "<tr> <td>{$row["name"]}</td><td>{$row["id"]}</td><td>{$row["format"]}</td><td><input type='radio' name='selected' value='{$row["id"]}'".$default."></td></tr>";
}
if (!$found) echo "You have no decks.";
echo "<input type='submit' name='submit' value='Update'></form> <div id=0><button onclick='createdeck();'>Create a deck</button> </div>";
mysql_close($con);

?>

</body>

</html>

