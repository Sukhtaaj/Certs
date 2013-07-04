<html>
<body>

<?php
require_once('dbase.php');

$csv = $_FILES["file"]["name"];

move_uploaded_file($_FILES["file"]["tmp_name"],$_FILES["file"]["name"]);

$file = fopen("$csv","r"); 
$var=0;

mysql_query("TRUNCATE TABLE `csv`");
while($list = fgetcsv($file))
{

$check = mysql_query("SELECT * FROM `csv`");
	while($row=mysql_fetch_array($check))
	{
		if($list[0] == $row['sal'] && $list[1] == $row['first_name'] && $list[2] == $row['middle_name'] &&
		$list[3] == $row['last_name'] && $list[4] == $row['institute'] && $list[5] == $row['city'] &&
 		$list[6] == $row['state'] && $list[7] == $row['photo'])
		{ 
			$var++;
		}
	}
if($var==0)



	{
	mysql_query("INSERT INTO `csv` VALUES('','$list[0]','$list[1]','$list[2]','$list[3]','$list[4]','$list[5]','$list[6]','$list[7]')");
	}

}
fclose($file); 
echo 'file '.$csv.' imported successfully.';
?>

<form action="csvcert.php" method="POST" enctype="multipart/form-data">
<p>Photos folder must be in <strong>(.zip)</strong> or <strong>(.tar.gz)</strong> format.<br><br> 
Select :<input type="file" name="photo">
<input type="submit" value="Next">
</form>

</body>
</html> 
