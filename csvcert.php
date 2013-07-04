<html>
<body>
<?php

require_once('../library/odf.php');
require_once('dbase.php');


$odf = new odf("new.odt");


$file = $_FILES["photo"]["name"];

move_uploaded_file($_FILES["photo"]["tmp_name"],$_FILES["photo"]["name"]);

exec("tar -zxvf $file");

$token = strtok($file,".");


//Selecting the user entered data from database and replacing with the tags in odt document. 

$result = mysql_query("SELECT * FROM `csv`");

$article = $odf->setSegment('articles');
while($row = mysql_fetch_array($result))
{
	
		 //image
            
                $pic = "$token/".$row['photo'].".jpg";
		if(!file_exists($pic))
                  {
                  $pic = "Uploads/image.gif";
                 }

                $article->setImage('pic',$pic,4);
		
		//name
                if($row['middle_name']==NULL)
		         $article->nameArticle(" ".$row['sal']." ".$row['first_name']." ".$row['last_name']);
		else
                         $article->nameArticle(" ".$row['sal']." ".$row['first_name']." ".$row['middle_name']." ".$row['last_name']); 
		//department
		$article->deptArticle($row['institute'].", ".$row['city']);
	
	$article->merge();			
}	

$odf->mergeSegment($article);


// We save the file
$odf -> saveToDisk("cert.odt"); 
?>
<form action="cert.odt">
<input type="submit" value="Download ODT">
</form>

<form action="pdf.php">
<input type="submit" value="Download PDF">
</form>

<form action="index.html">
<input type="submit" value="Goto First Page">
</form>

</body>
</html>
