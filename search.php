<?php session_start(); ?>
<!doctype html>
<html>
<head>
<title>Deckbuilder</title>
<script type="text/javascript">
function contains(arr,obj) {
    return ((arr.indexOf(obj) != -1)||arr.indexOf(obj.toString()) != -1);
}

function change(id) {
	var elem = document.getElementsByName("changed")[0];
	if (elem.value == '') elem.value+=id;
	else {
		var list = elem.value.split(",");
		if (!contains(list,id)) { 
			list.push(id);
			elem.value=list.join(",");
		}
	}
}

function add(id, num) {
	var elem = document.getElementById("00"+id);
	
	elem.value = Math.max(parseInt(elem.value) + num, 0);
	if (num) change(id);
}
</script>
</head>
<body>
<?php
function realid($row) {
	if (isset($row[Nback_id])) {
		if (substr($row[Nnumber], -1, 1) == 'b')
		return $row[Nback_id];
	
	}
	return $row[Nid];
}

function symbolize($raw_str) {
	$raw_str = preg_replace('/\{(\d+|[WUBRGX])\}/', "<img src='imgs/assets/mana/mana$1.gif'>", $raw_str);
	return str_replace(array("{T}","{Q}"),array("<img src='imgs/assets/tap.gif'>", "<img src='imgs/assets/untap.gif'>"), $raw_str);
}

function format($raw_str) {
	return str_replace(array("£","#_","_#"),array("<br />","<i>","</i>"),$raw_str);

}

function search_query() {
	$db_name = array("Name"=>"Nname", "Text"=>"Nability", "Type"=>"Ntype");
	$first = True;
	$query = "SELECT * FROM ncards WHERE";
	foreach ($_GET["where"] as $place) {
		if (!$first) $query = $query . " OR";
		else $first = False;
		$query = $query." {$db_name[$place]} LIKE '%{$_GET["query"]}%'";
	}
	return $query;
}

echo "<a href='index.php'> &lt;&lt; Go back </a> <br />";

if(!isset($_SESSION["selected"])) $disabled = "disabled='disabled'";
else $disabled = "";

if(strlen($_GET["query"])<3) die("Input is too short!");
$con = mysql_connect("localhost", "root", "");
if (!$con)
  {
  die('Error: ' . mysql_error());
  }
$db_selected = mysql_select_db("deckbuilder", $con);

$result = mysql_query(search_query());
if (!$result) die('Error: ' . mysql_error());

echo "Card search result for <b>{$_GET["query"]}</b> in ".implode(" or ", $_GET["where"]).":";
$found = false;
while($row = mysql_fetch_array($result))	{
	if (!$found) echo "<form method='POST' action='managedeck.php'><input type='hidden' name='updatedeck' value='true'><input type='submit' name='submit' value='Update' {$disabled}><input type='hidden'  name=changed value=''><br /><table border=1><tr><th>Image</th><th>Name</th><th>Type</th><th>Cost</th><th>Text</th><th>Your Deck</th> </tr>";
	$found = true;
	if ($row["Nback_id"]) $img = "<img src='imgs/{$row[Nset]}/{$row[Nid]}.full.jpg' id={$row[Nid]} height=215 width=149 onclick=\"this.src='imgs/{$row[Nset]}/'+(".($row[Nback_id]+$row[Nid])."-this.id)+'.full.jpg';this.id=".($row[Nback_id]+$row[Nid])."-this.id\">";
	else $img = "<img src='imgs/{$row[Nset]}/{$row[Nid]}.full.jpg' height=215 width=149>";
	$id = realid($row);
	echo "<tr><td>{$img}</td><td>{$row[Nname]}</td><td>{$row[Ntype]}</td><td>".symbolize($row[Nmanacost])."</td><td>".format(symbolize($row[Nability]))."</td><td><input type=textbox {$disabled} value=0 onchange='change(this.id);' id="."00".$row[Nid]." name='{$id}'><br /><button type='button' {$disabled} onclick='add({$row[Nid]},1);'>Add 1</button><button type='button' {$disabled} onclick='add({$row[Nid]},-1);'>Remove 1</button><br/><button type='button' {$disabled} onclick='add({$row[Nid]},4);'>Add 4</button><button type='button' {$disabled} onclick='add({$row[Nid]},-4);'>Remove 4</button></td></tr>\n";
}
if ($found) {
	echo "</table></form>";
}
else echo "No results found.";
mysql_close($con);

?>
</body>
</html>