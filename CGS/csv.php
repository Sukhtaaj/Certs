<?php session_start(); ?>
<?php

require_once('../library/odf.php');

$csv = $_FILES["file"]["name"];

/******************************** csv File input validation********************************************/
//Link to other file in case any of folllowing conditions fail
$url = "<meta http-equiv='Refresh' content='1; URL=option.php?var=csv'>";

if($csv == NULL)		//checks if no file is selected
{
echo "<center><h2>NO (.csv) File Selected</h2></center>";
echo $url;
exit;
}
else
{
$ext = strrchr($csv,".");

if($ext != ".csv")		//checks if csv format file is not selected
{
echo "<center><h2>Invalid File Format for .csv file...</h2></center>";
echo $url;
exit;
}
move_uploaded_file($_FILES["file"]["tmp_name"],"uploads/csv/data/".$_FILES["file"]["name"]);
} 

  $base = $_SESSION["base"];		//Getting file name with filled Institute Details
  $odf = new odf("odt/base/$base.odt"); //Initializing the object with above file name


  $file = $_FILES["photo"]["name"];
  if($file == NULL)		//checks if no file is selected
  {
  echo "<center><h2>No compressed file selected for images<h2></center>";
  echo $url;
  exit;
  }

  move_uploaded_file($_FILES["photo"]["tmp_name"],"uploads/csv/images/".$_FILES["photo"]["name"]);
  
  $extension = strrchr($file,".");	//using strrchr() to fetch the extension of the file
  
  chdir('uploads/csv/images');		//Changing the directory to extract the files at that location
  
  if ($extension == ".gz")
    {
      //extracting the tar.gz file;
      exec("tar -zxvf $file");
    }
  elseif($new == ".zip")
    {
     //extracting the zip file;
     exec("unzip $file");
    }
  else
    {
     echo "<center><strong>Invalid File Format for compressed images Folder.</strong></center>";
     echo $url;
     exit;      
}
chdir('../../..');		//changing directory back to previous 

$dest =  strtok($file,".");	//using strtok for storing the filename without extension
unlink("uploads/csv/images/$file");  //After Extracting Deleting the compressed file 


$csvfile = fopen("uploads/csv/data/$csv","r");	//Opening csv file in read mode

$article = $odf->setSegment('articles');	//Defining Segment articles( used in .odt file)
while($result = fgetcsv($csvfile))		//Fetching data in each row of csv file to array $result
{
	
		 //image
            
                $pic = "uploads/csv/images/$dest/".$result[7];
		if(!file_exists($pic))
                  {
                  $pic = "uploads/manual/image.gif";
                 }

                $article->setImage('pic',$pic,4);
		
		//name
                if($result[2] == NULL)
		         $article->nameArticle(" ".$result[0]." ".$result[1]." ".$result[3]);
		else
                         $article->nameArticle(" ".$result[0]." ".$result[1]." ".$result[2]." ".$result[3]); 
		//department
		if($result[5] == NULL)
			$article->deptArticle($result[4].", ".$result[6]);
		else
			$article->deptArticle($result[4].", ".$result[5]);
	
	$article->merge();		//Ending the current segment
}	

$odf->mergeSegment($article);		//Ending the segment Object

$certName = uniqid();		//Using Function Uniqid() for unique name every File generated
// We save the file
$odf -> saveToDisk("odt/cert/$certName.odt"); 

//copying the odt file to be converted to PDF
copy("odt/cert/$certName.odt", "../odt2pdf/cde-root/home/sukhdeep/Desktop/$certName.odt");

//changing Directory
chdir('../odt2pdf/cde-root/home/sukhdeep');

//Command for conversion to PDF
$myCommand = "./libreoffice.cde --headless --convert-to pdf:writer_pdf_Export Desktop/$certName.odt --outdir Desktop/";
exec ($myCommand);

//Copying the converted file to the PDF folder
copy("Desktop/".str_replace(".odt", ".pdf", "$certName.odt"), "../../../../CGS/pdf/".str_replace(".odt", ".pdf", "$certName.odt"));
//Deleting converted files after copying
unlink("Desktop/$certName.pdf");
unlink("Desktop/$certName.odt");


echo   '<html>
	<head>
	<link href="style/bootstrap.min.css" rel="stylesheet" media="screen">	
	<link href="style/style.css" rel="stylesheet" media="screen">	
	</head>
	<body>
	<center>
	<h1>Your Certificate has been Generated!</h1>
	<form action="odt/cert/'.$certName.'.odt">
	<input class="btn btn-primary" type="submit" value="Download ODT">
	</form>

	<form action="pdf/'.$certName.'.pdf">
	<input class="btn btn-primary" type="submit" value="View/Download PDF">
	</form>

	<form action="index.html">
	<input class="btn btn-primary" type="submit" value="Goto First Page">
	</form>
	</center>
	</body>
	</html>';

exit;
?>
