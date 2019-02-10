<?php
$imagedis='';
// display all errors on the browser
error_reporting(E_ALL);
ini_set('display_errors','On');
require_once 'demo-lib.php';
demo_init(); // this just enables nicer output

// if there are many files in your Dropbox it can take some time, so disable the max. execution time
set_time_limit( 0 );
require_once 'DropboxClient.php';
$dropbox = new DropboxClient( array(
	'app_key' => "n3vjnz16irz93ew",      // Put your Dropbox API key here
	'app_secret' => "5jnfgnjaxq3w4vl",   // Put your Dropbox API secret here
	'app_full_access' => false,
) );
$return_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?auth_redirect=1";

// first, try to load existing access token
$bearer_token = demo_token_load( "bearer" );

if ( $bearer_token ) {
	$dropbox->SetBearerToken( $bearer_token );
	// echo "loaded bearer token: " . json_encode( $bearer_token, JSON_PRETTY_PRINT ) . "\n";
} elseif ( ! empty( $_GET['auth_redirect'] ) ) // are we coming from dropbox's auth page?
{
	// get & store bearer token
	$bearer_token = $dropbox->GetBearerToken( null, $return_url );
	demo_store_token( $bearer_token, "bearer" );
} elseif ( ! $dropbox->IsAuthorized() ) {
	// redirect user to Dropbox auth page
	$auth_url = $dropbox->BuildAuthorizeUrl( $return_url );
	die( "Authentication required. <a href='$auth_url'>Continue.</a>" );
}

// echo json_encode( $dropbox->GetAccountInfo(), JSON_PRETTY_PRINT );
if (isset($_FILES['image'])) {
  $file = $_FILES['image'];
  $file_name=$_FILES['image']['name'];
  $tmp= $_FILES['image']['tmp_name'];
  // $path="uploads/";
  // $target_file = $path . basename($_FILES["image"]["name"]);
  // move_uploaded_file($_FILES['image']['tmp_name'],$target_file);
  //   // code...
  //

  $dropbox->UploadFile($tmp,$file_name);
  // $files = $dropbox->GetFiles("",false);
  // $data=$dropbox->GetMetadata("https://api.dropboxapi.com/1/metadata/auto/");
  // print_r( array_keys( $data ) );

}
if (isset($_POST['delete'])) {
// 	if (array_key_exists($_GET['delete'],$allfiles)) {
// 		// code...
//
// 	$dimage=$allfiles[$_GET['delete']];
// 	// $dest_file = "uploads/".basename( $dimage->path );
// 	$p=$dimage->path;
// 	$dropbox->Delete($p);
// 	// unset($dimage);
// 	// print_r($dimage);
// 	// header("Location: album.php");
// 	echo("<meta http-equiv='refresh' content='0'>");
// }
$dropbox->Delete("/".$_POST['delete']);
// $delete="download_".$_POST['delete'];
// unlink($delete);
}
// if (isset($_POST['display'])) {
// 	$files1 = $dropbox->GetFiles("",false);
// 	$f=$files1[$_POST['display']];
// 	$dis=$files1[$_POST['display']]->path;
// 	$dn=$files1[$_POST['display']]->name;
// 	// print_r($dis);
// 	$test_file = "download_" . basename( $dis );
// 	$dropbox->DownloadFile($f,$test_file);
// 	// error_reporting(E_ERROR | E_PARSE);
// 	$imagedis="download_".$dn;
// }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Album</title>
  </head>
  <body style="background-color:#49494a">
    <form class="" method="post" enctype="multipart/form-data" style="width:80%; margin:auto;text-align:center; background-color:white; border-radius:10px;box-shadow: 5px 10px black;">
      <input type="file" name="image" value="" size='100'>
      <button type="submit" name="button">Upload</button>
    </form>
		<form class="" action="album.php" method="post" id="images">
		</form>
		<?php
		$files = $dropbox->GetFiles("",false);
		$allfiles=array();
		if ( ! empty( $files ) ) {
			// $file      = reset( $files );
			echo '<div style="width:40%; margin-top:80px; height:300px;margin-left:100px;text-align:center; border-radius:10px; background-color:white; display:inline; float:left; overflow-y:scroll;box-shadow: 5px 10px black;">';
			echo "<h4>Scroll to view all</h4>";
			foreach ($files as $file) {
				// code...
			// $test_file = "test_download_" . basename( $file->path );

			// echo "\n\n<b>Meta data of <a href='" . $dropbox->GetLink( $file ) . "'>$file->path</a>:</b>\n";
			// print_r( $dropbox->GetMetadata( $file->path ) );
			$image= $dropbox->GetMetadata($file->path);
			// echo "<p><a href=''>".(string)$image->name."</a></p>";
			$imagename=(string)$image->name;
				$allfiles[$imagename]=$file;
			$link=	$dropbox->GetLink($file, $preview=false)	;
			// print_r($link);
			echo "<p><button onclick='display(this);'style='border:none; margin-right: 10px; width:70%; height: 30px;background-color:#337ab7; border-radius:10px; font-weight:bold; overflow-x:hidden;' type='submit' name='display' value='".$link."'>".$imagename."</button><button type='submit' name='delete' form='images' value='".$imagename."' style='background-color:#5cb85c; border-radius:5px;'>DELETE</button></p>";
			// echo "<p><button style='border:none' type='submit' name='display' form='images' value='".$imagename."'>".$imagename."</button></p>";

			// echo "\n\n<b>Downloading $file->path:</b>\n";
			// print_r( $dropbox->DownloadFile( $file, $test_file ) );
			//
			// echo "\n\n<b>Uploading $test_file:</b>\n";
			// print_r( $dropbox->UploadFile( $test_file ) );
			// echo "\n done!";
			//
			// echo "\n\n<b>Revisions of $test_file:</b>\n";
			// print_r( $dropbox->GetRevisions( $test_file ) );
			// echo "<p></p>";
		}
		echo "</div>";
		// if (!$imagedis=='') {
		// 	echo "<p style='width:40%; margin:auto;text-align:center; display:inline'><img src='".$imagedis."' style='width:450px; height:450px; border-radius:10px; box-shadow: 5px 10px black;'></img></p>";
		// }
	}
		 ?>
		 <p style='width:40%; margin:auto;text-align:center; display:inline; position:relative; right:5%' id='imgbox'></p>
<script type="text/javascript">
function display(ele) {
	document.getElementById('imgbox').innerHTML='';
	var imgsrc = ele.value;
	var img=document.createElement("img");
	img.style.cssText='width:450px; height:450px; border-radius:10px; box-shadow: 5px 10px black;'
	// document.getElementById('preview').setAttribute('src',imgscr);
	img.src=imgsrc;
	document.getElementById('imgbox').appendChild(img);
}
</script>
  </body>
</html>
