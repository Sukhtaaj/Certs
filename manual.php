<?php session_start(); ?>
<?php

require_once('../library/odf.php');
require_once('dbase.php');

$odf = new odf("new.odt");
if ($_SERVER['REQUEST_METHOD'] == 'POST')  //My condition;
{
// Assigning Form data to variables.
$var =0;
$name = $_POST["sal"];
$firstName = $_POST["fname"];
$middleName = $_POST["mname"];
$lastName = $_POST["lname"];
$institute = $_POST["ins"];
$city = $_POST["city"];
$state = $_POST["state"];

$_SESSION['name'] = $name;
$_SESSION['fname'] = $firstName;
$_SESSION['mname'] = $middleName;
$_SESSION['lname'] = $lastName;
$_SESSION['ins'] = $institute;
$_SESSION['city'] = $city;
$_SESSION['state'] = $state;



//assigning image name to variable photo.
$photo = $_FILES["file"]["name"]; 
$_SESSION['photo'] = $photo;

/*************************************** Image Validation********************************************/
$url = "<meta http-equiv='Refresh' content='1; URL=option.php?var=manual'>";
if (($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/jpg")
|| ($_FILES["file"]["type"] == "image/pjpeg")
|| ($_FILES["file"]["type"] == "image/x-png")
|| ($_FILES["file"]["type"] == "image/png"))
{
if($_FILES["file"]["size"] > 1000000)
{
echo "<center><strong>Image Size Exceeded...</strong></center>";
sleep (2);
echo $url;
exit;
}
}
else
{
echo "<center><strong>No OR Invalid Image File...</strong></center>";
sleep (2);
echo $url;
exit;
}


//Moving uploaded file to uploads directory on server.
move_uploaded_file($_FILES["file"]["tmp_name"],"uploads/" . $_FILES["file"]["name"]);
copy("uploads/".$_FILES["file"]["name"],"uploads/src.jpg");

//Condition check for redundancy of data to be added to database. 
$check = mysql_query("SELECT * FROM `data`");

while($row = mysql_fetch_array($check))
{
                 if($name == $row['sal'] && $firstName == $row['first_name'] && $middleName == $row['middle_name'] && 
                  $lastName == $row['last_name'] && $institute == $row['institute'] && $city == $row['city'] && 
                  $state == $row['state'] && $photo == $row['photo'])
                           {
                                $var++;
                           }
}

//Inserting data into database after redundancy check. 
if($var == 0)
{
                  mysql_query("INSERT into `data` VALUES('','$name','$firstName','$middleName','$lastName','$institute','$city','$state','$photo')");
}

}

else //Else Condition	`			

{
	$name = $_SESSION['name'];
	$fname = $_SESSION['fname'];
	$mname = $_SESSION['mname'];
	$lname = $_SESSION['lname'];
	$ins = $_SESSION['ins'];
	$city = $_SESSION['city'];
	$state = $_SESSION['state'];
	$photo = $_SESSION['photo'];

        $targ_w = $targ_h = 500;
	$jpeg_quality = 100;

	$src = "uploads/$photo";
	$img_r = imagecreatefromjpeg($src);
	$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

	imagecopyresampled($dst_r,$img_r,0,0,$_GET['x'],$_GET['y'],
	$targ_w,$targ_h,$_GET['w'],$_GET['h']);

	imagejpeg($dst_r,"uploads/cropped/$photo",$jpeg_quality);


//Selecting the user entered data from database and replacing with the tags in odt document. 

$result = mysql_query("SELECT * FROM `data` WHERE sal = '$name' AND first_name = '$fname' AND middle_name = '$mname'
 AND last_name = '$lname' AND institute = '$ins' AND city = '$city' AND state = '$state' AND photo = '$photo'");

$article = $odf->setSegment('articles');
while($row = mysql_fetch_array($result))
{
	
		 //image
            
                $pic = "uploads/cropped/".$row['photo'];
                
                if(!file_exists($pic))
                  {
                  $pic = "Photos/image.gif";
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

//copying the file to be converted
copy("cert.odt", "../../Convert/cde-root/home/sukhdeep/Desktop/certificate.odt");

//changing Directory
chdir('../../Convert/cde-root/home/sukhdeep');

//Command for conversion to PDF
$myCommand = "./libreoffice.cde --headless -convert-to pdf Desktop/certificate.odt -outdir Desktop/";
exec ($myCommand);


copy("Desktop/".str_replace(".odt", ".pdf", "certificate.odt"), "../../../../Demo/testReduce/pdf/".str_replace(".odt", ".pdf", "certificate.odt"));



echo   '<html>
	<body>
	<form action="cert.odt">
	<input type="submit" value="Download ODT">
	</form>

	<form action="pdf/certificate.pdf">
	<input type="submit" value="View/Download PDF">
	</form>
	
	<form action="option.php" method = "GET">
	<input type="submit" value="Generate Another Certificate">
	</form>
	<form action="index.php">
	<input type="submit" value="Goto First Page">
	</form>
	</body>
	</html>';

exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Live Image Selector</title>
  <h1><center>Select from the image</center></h1>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <script src="jcrop/jquery.min.js"></script>
  <script src="jcrop/jquery.Jcrop.js"></script>
  <link rel="stylesheet" href="jcrop/main.css" type="text/css" />
  <link rel="stylesheet" href="jcrop/demos.css" type="text/css" />
  <link rel="stylesheet" href="jcrop/jquery.Jcrop.css" type="text/css" />

<script type="text/javascript">

  $(function(){

    $('#cropbox').Jcrop({
      aspectRatio: 1,
      setSelect:   [50, 0, 600,600],
      allowResize: false,
      onSelect: updateCoords
    });

  });

  function updateCoords(c)
  {
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);
  };

  function checkCoords()
  {
    if (parseInt($('#w').val())) return true;
    alert('Please select a crop region then press submit.');
    return false;
  };

</script>
<style type="text/css">
  #target {
    background-color: #ccc;
    width: 500px;
    height: 330px;
    font-size: 24px;
    display: block;
  }


</style>

</head>
<body>

<div class="container">
<div class="row">
<div class="span12">
<div class="jc-demo-box">



		<!-- This is the image we're attaching Jcrop to -->
		<img src="uploads/src.jpg" id="cropbox" />

		<!-- This is the form that our event handler fills -->
		<form action="manual.php" method="get" onsubmit="return checkCoords();">
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />
			<input type="submit" value="Crop Image" class="btn btn-large btn-inverse" />
		</form>

		<p>
			<b>Image Cropping Area.</b>Highlighted portion of the image will be selected.
		</p>


	</div>
	</div>
	</div>
	</div>
	</body>

</html>
