<html>
<body>

<?php

require_once('../library/odf.php');
require_once('dbase.php');

$csv = $_FILES["file"]["name"];
/******************************** csv File input validation********************************************/

$url = "<meta http-equiv='Refresh' content='1; URL=option.php?var=csv'>";
if($csv == NULL)
{
echo "<center><h2>NO (.csv) File Selected</h2></center>";
echo $url;
exit;
}
else
{
$csvtoken = strtok($csv,".");
  while ($csvtoken != false)
    {
      $ext = $csvtoken;
      $csvtoken = strtok(".");
    }
if($ext != "csv")
{
echo "<center><h2>Invalid File Format for .csv file...</h2></center>";
echo $url;
exit;
}
move_uploaded_file($_FILES["file"]["tmp_name"],$_FILES["file"]["name"]);
}

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
//echo 'file '.$csv.' imported successfully.';
unlink("$csv");

  
  $odf = new odf("new.odt");


  $file = $_FILES["photo"]["name"];
  if($file == NULL)
  {
  echo "<center><h2>No compressed file selected for images<h2></center>";
  echo $url;
  exit;
  }

  move_uploaded_file($_FILES["photo"]["tmp_name"],$_FILES["photo"]["name"]);
  $token = strtok($file,".");
  while ($token != false)
    {
      $new = $token;
      $token = strtok(".");
    }

  if ($new == "gz")
    {
      //echo "tar.gz file";
      exec("tar -zxvf $file");
    }
  elseif($new == "zip")
    {
     //echo "zip file";
     exec("unzip $file");
    }
  else
    {
     echo "<center><strong>Invalid File Format for compressed images Folder.</strong></center>";
     echo $url;
     exit;      
}

$dest =  strtok($file,".");
unlink("$file");

//Selecting the user entered data from database and replacing with the tags in odt document. 

$result = mysql_query("SELECT * FROM `csv`");

$article = $odf->setSegment('articles');
while($row = mysql_fetch_array($result))
{
	
		 //image
            
                $pic = "$dest/".$row['photo'];
		if(!file_exists($pic))
                  {
                  $pic = "uploads/image.gif";
                 }

                $article->setImage('pic',$pic,4);
		
		//name
                if($row['middle_name']==NULL)
		         $article->nameArticle(" ".$row['sal']." ".$row['first_name']." ".$row['last_name']);
		else
                         $article->nameArticle(" ".$row['sal']." ".$row['first_name']." ".$row['middle_name']." ".$row['last_name']); 
		//department
		if($row['city']==NULL)
			$article->deptArticle($row['institute'].", ".$row['state']);
		else
			$article->deptArticle($row['institute'].", ".$row['city']);
	
	$article->merge();			
}	

$odf->mergeSegment($article);


// We save the file
$odf -> saveToDisk("cert.odt"); 
copy("cert.odt", "../../cde-package/cde-root/home/sukhdeep/Desktop/certificate.odt");
$var1 = chdir('../../cde-package/cde-root/home/sukhdeep');
//echo $var1;
$myCommand = "./libreoffice.cde --headless -convert-to pdf Desktop/certificate.odt -outdir Desktop/";
$var2 = exec ($myCommand);
//echo $var2;

copy("Desktop/".str_replace(".odt", ".pdf", "certificate.odt"), "../../../../Demo/test/pdf/".str_replace(".odt", ".pdf", "certificate.odt"));

echo   '<h1>Your Certificate has been Generated!<h/1>
	<body background="html/bck.jpg">
	<center>
	<form action="cert.odt">
	<input type="submit" value="Download ODT">
	</form>

	<form action="pdf/certificate.pdf">
	<input type="submit" value="View/Download PDF">
	</form>

	<form action="index.html">
	<input type="submit" value="Goto First Page">
	</form>
	</center>
	</body>';

exit;

?>


</body>
</html> 
