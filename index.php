
<html> 
	<head> 
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" /> 
		<title>Upload</title> 
		<link rel="stylesheet" href= "link/style.css" type="text/css" /> 
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
			<script src="link/mod.js"></script>		
	</head> 
	<body> 
		<?php if(!isset($_GET["edit"])){
		echo '
		<div> 
		<form enctype="multipart/form-data" action="index.php" method="POST"> 
		<input name="plik" type="file" accept="text/xml,application/x-zip-compressed" /> 
		<input type="submit" value="Wyślij plik" /> 
		</form> 
		</div>';

		include_once 'lib/common.php';

		if(isset($_FILES)&&isset($_FILES['plik']))
		{
			$plik_tmp = $_FILES['plik']['tmp_name']; 
			$plik_nazwa = $_FILES['plik']['name']; 
			$plik_rozmiar = $_FILES['plik']['size']; 
			$plik_typ = $_FILES['plik']['type'];
			$error ="";
			if(is_uploaded_file($plik_tmp)) { 
				if($plik_typ === "application/x-zip-compressed"){
					$zip = new ZipArchive;
					$files = array();
					if ($zip->open($plik_tmp) === TRUE) {
						for ($i = 0; $i < $zip->numFiles; $i++) {
							$filename = $zip->getNameIndex($i);
							//echo $filename." exp = ".strtolower(substr($filename, -4, 4))."  </br> ";
							if(strtolower(substr($filename, -4, 4)) === strtolower(".xml") ){
								$files[]=$filename;
								//echo "echo 2 =".$filename."</br>";
							}
						}
						if(count($files)>0){
							//echo "echo 3 </br>";
							$zip->extractTo('upload',$files);// jak zrobić żeby rozpakowywał tylko pliki xml, a jak takich nie ma to żeby był error
						}else{
							$error = "brak pliku XML w pobranym archiwum!!";
						}
						$zip->close();
						//echo 'ok';
					} else {
						$error = "nie mozna otwozyć pobranego pliku!!";
					}//*/
				}else{
					move_uploaded_file($plik_tmp, "upload/$plik_nazwa"); 
				}
				if($error === ""){
					Header( 'HTTP/1.1 301 Moved Permanently' );
					Header( 'Location: index.php');
				}else{
					echo $error;
				}
			} 
		}// if file

		if(isset($_GET["del"])){
			$file = $_GET["del"];
			$_file="upload/".$file;
			if(file_exists($_file)) unlink($_file);
		}elseif (isset($_GET["get"])){
			$file_to_download = "upload/".$_GET["get"];
			header("Content-Length: " . filesize($file_to_download));
			header("Content-Transfer-Encoding: binary");
			header("Content-Disposition: attachment; filename=" . basename($file_to_download));
			readfile($file_to_download);
		}
		Pliki();
		}//if not edit
		else{
			if(isset($_GET["edit"])){
				$file = $_GET["edit"];
				$_file="upload/".$file;
				if(!file_exists($_file)){
					echo 'plik nie istnieje';
					exit();
				}
				$deXml = simplexml_load_file($_file);
				if(isset($_GET["zer"])){
					$ilosc= count($deXml->studenci->student);
					for($i=0;$i<$ilosc;$i++){
						$deXml->studenci->student[$i]->daneDotyczaceStudiow->semestry->semestr->ects->ectsUzyskane =0;				
						$ret = extractPesel($deXml->studenci->student[$i]->osoba->pesel);
						$deXml->studenci->student[$i]->osoba->addChild('plec',$ret["płeć"]);
						$deXml->studenci->student[$i]->osoba->addChild('rokUrodzenia',$ret["rok"]);
					}
					$deXml->asXML("upload/2_".$file);// ok mamy zapis do xml :) pozostała do zrobienia modyfikacja ręczna, automatyczna działa
					Header( 'HTTP/1.1 301 Moved Permanently' );
					Header( 'Location: index.php');
				//*/
				}elseif(isset($_GET["xml"])){
					echo "<pre>";
					print_r($deXml);
					echo "</pre>";
				}else{
					$X_Arr = xmlTosimpleArray($deXml->studenci->student);			
					GenerujTabele($X_Arr);			
					/*
					echo "<pre>";
					print_r(HeadArray($X_Arr));
					print_r($X_Arr);
					//print_r($deXml);
					echo "</pre>";
					//*/			
				}
			}
		}
		?> 
	</body> 
</html>


