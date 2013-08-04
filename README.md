Certs
=====
--------library(Directory)-------- 

Contains 
odf.php -> Main php file of odtphp for handling odf objects

Segment.php -> php file for handling Segments

SegmentIterator.php -> php file for iterating Segments

  -zip(Directory)
	Contains PclZipProxy.php, PhpZipProxy.php, ZipInterface.php 

-pclzip(diectory)
	     Contains pclzip.lib.php
Files in zip directory are for dealing with odt format file 
as (.odt)  is a compressed file containing a number of files in it.

--------CGS(Directory)--------

Contains
index.html -> Html file for the First Page.

index.php -> php file for displaying input forms for Institute Details 
	     and manipulating the selected design with user Input values
	     showing manual or Csv file data Entry Options.

option.php ->php file for Displaying the Manual data Entry page or csv file
	    data entry page according to option selected by the User.

manual.php -> Displays the Image cropping tool and then saves the cropped
	      image and then Uses the User entered data and cropped photo
	      to produce the final single certificate in odt as well as PDF format.

csv.php -> Handles the csv file along with extracting the compressed file containing
	   images and at the end generates the batch certificate file for all data 
	   entries in the csv file.

sample.zip -> sample file used for csv File upload module.
  
  -jcrop(Directory)-
    Contains files needed for the cropping tool used in the manual data entry Module.

  -odt(Directory)-
   Contains
     -base(Directory) -> File with Filled Institute details is saved in this directory.
     -cert(Directory) -> Final odt Certificate file is saved in this directory.
     -design(Directory) -> Contains the odt file corresponding to design selected by user.

  -pdf(Directory)-
    Contains all the certificate PDF files produced.

  -style(Directory)-
	  Contains css files for styling along with images to be used in front html page.

  -uploads(Directory)-
	  Contains files uploaded by user.
     -manual(Directory) -> Images uploaded by user are stored here.
       -cropped(Directory) -> Cropped images are stored here.
     -csv(Directory)
       -data(Directory) -> Stores the csv data file uploaded by user
       -images(Directory) -> Compressed file containing images is stored and extracted here.
