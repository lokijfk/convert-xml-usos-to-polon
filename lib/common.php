<?php



//ok
/**
 * wyciąga z pesela datę urodzenia i płeć (zrobione na podstawie kodu z IRKa)
 * @param unknown $pesel
 * @return string|multitype:string
 */
function extractPesel($pesel){
	if (!preg_match("/^[0-9]+$/", $pesel))// funkcja przestarzała, pewnie trzeba będzie podmienić, ale mamy wzór ciągu porównań
		return ('zabronione znaki');
	if (strlen($pesel) != 11)
		return ('zla długość');

	if ($plec) {
		$c = substr($pesel, 9, 1);
		if ($c % 2 == 0 && $plec != KOBIETA)
			return TXT('niezgodność płci');
		if ($c % 2 == 1 && $plec != MEZCZYZNA)
			return TXT('niezgodność płci');
	}

	$rok = substr($pesel, 0, 2);
	if (substr($rok, 0, 1) == "0")
		$rok = substr($rok, 1, 1);
	$miesiac = substr($pesel, 2, 2);
	if (substr($miesiac, 0, 1) == "0")
		$miesiac = substr($miesiac, 1, 1);
	$dzien = substr($pesel, 4, 2);
	if (substr($dzien, 0, 1) == "0")
		$dzien = substr($dzien, 1, 1);

	if ($miesiac > 0 && $miesiac < 20)
		$rok = 1900 + $rok;
	if ($miesiac > 20 && $miesiac < 40)
		$rok = 2000 + $rok;
	if ($miesiac > 40 && $miesiac < 60)
		$rok = 2100 + $rok;
	if ($miesiac > 60 && $miesiac < 80)
		$rok = 2200 + $rok;
	if ($miesiac > 80 && $miesiac < 100)
		$rok = 1800 + $rok;
	$miesiac = $miesiac % 20;

	$data = "".$rok."-".$miesiac."-".$dzien;
	//sprawdzić jakie mają być oznaczenia płci i takiw wpisać
	$c = substr($pesel, 9, 1);
	if ($c % 2 == 0)/* && $plec != KOBIETA)*/ $plec ="K";

	if ($c % 2 == 1)/* && $plec != MEZCZYZNA)*/ $plec ="M";
		
	return array("rok"=>$rok,"płeć"=>$plec);

}




//fail
/**
 * Utworzenie obiektu typu $id - skopiowany z robaków, zmienic na potrzeby XML
 */
function _getObject($id, $option = 0)
{
	/*
	 * będziemy przekazywać tablicę GET, loader musi mieć gdzieś wpisane jakie pola ma sprawdzać, może jako drugi parametr
	 * albo zmienić styl zapytań, zrobić action=edit&file=kupa.xml 
	 * i jak dalej będzie jakiś xml to już dalej będzie się o to martwił wywołany kontroler
	 *  albo jak będzie czegoś brakować to tak samo, jest to sprawa kontrolera
	 */	
	if (!preg_match("/^[a-zA-Z0-9_]{1,32}$/", $id)){
		$rej = Rejestrator::instance();
		$rej->set("ERROR", " - niewłaściwy zestaw znaków  w wywoływanej akcji");
		return NULL;
	}
	$dir = "lib/class/";
	$dir2 = "lib/MVC/";
	$suffix = ""; //dotyczy klasy
	$prefix = "kon."; // dotyczy pliku z klasą
	$php_filename = $dir . $prefix . $id . ".php";
	$php_class = $id . $suffix;
	if (!file_exists($php_filename)){
		$php_filename = $dir2 . $prefix . $id . ".php";
		if (!file_exists($php_filename)){
			$rej = Rejestrator::instance();
			$rej->set("ERROR", "- próba wywołanie niewłaściwej akcji ");
			return NULL;
		}
	}
	include_once($php_filename);
	if ($option)
		$obj = new $php_class($option);
	else
		$obj = new $php_class;
	return $obj;	
}

//ok
/**
 * wyświetla znalezione pliki xml i z linkami do podgladu i edycji
 */
function Pliki()
{
	$katalog = "upload";
	$filenames = array();
	if ($handle = opendir($katalog))
	{
		while (false !== ($file = readdir($handle)))
		{
			$filenames[] = $file;
		}
		sort($filenames);
	}
	closedir($handle);
	if(count($filenames)>0){
		echo"<table border='1'><tr><td>nazwa</td><td>ostatnia modyfikacja</td><td>del</td><td>download</td><td>zerowanie</td>
			<td>XLM</td><td>kopia</td></tr>";
		$date = date_create();
		foreach($filenames as $file)
		{
			if (strstr(strtoupper($file),".XML"))
			{
				date_timestamp_set($date, filemtime($katalog.'/'.$file));
				$filetime= date_format($date, 'Y-m-d H:i:s');
				echo "<tr><td><a href='index.php?edit=$file'>".$file."</a></td><td>".
					$filetime."</td><td><a href='index.php?del=$file'>X</a></td><td><a href='index.php?get=$file'>D</a></td><td>
					<a href='index.php?edit=$file&zer=true'>Z</a></td><td><a href='index.php?edit=$file&xml=true'>XML</a></td><td>
					<a href='index.php?dup=$file'>K</a</td></tr>";
			}
		}//for
		echo "</table>";
	}//if

}

//fail
/**
 * podglad i edycja xml,  << do usunięcia >>
 * @param unknown $xml
 */
function show_xml($xml){
	echo"<table border='1'><caption align='center'>studenci</caption>";
	echo "<tr><td rowspan='3'>L.p.</td><td colspan='4'>osoba</td><td colspan='2'>dane Dotyczace Studiow</td></tr>";
	echo "<tr><td></td><td>osoba</td><td>osoba</td><td>osoba</td><td colspan='2'>dane Dotyczace Studiow</td></tr>";
	//echo "<tr><td></td><td><tab"
	$ilosc= count($xml->studenci->student);
	for($i=0;$i<$ilosc;$i++){
		echo "<tr><td>".$i."</td><td>";
		//osoba
		echo "</td><td>";
		//dane dotyczące studentów
		echo "</td></tr>";
		//$deXml->studenci->student[$i]->daneDotyczaceStudiow->semestry->semestr->ects->ectsUzyskane =0;

	}
	echo "</table>";
}

//ok
/**
 * konwersja z tablicy SimpleXMLElement Object do tablicy asocjacyjnej dwu wymiarowej
 * @param array SimpleXMLElement Object $xml
 * @return arrray
 */
function xmlTosimpleArray($xml){

	$ret = array();
	//if(is_array($xml)){
	$ile = count($xml);
	foreach($xml as $_xml){
		$ret[] = S_XML_EtoArray($_xml);
	}
	return $ret;
	//}else return "błędny parametr: to nie jest tablica";
}

//ok
/**
 * pobiera pojedyńczy  "rekord" xml i przerabia go na wiersz tablicy
 * @param SimpleXMLElement $x
 * @return array
 */
function S_XML_EtoArray(SimpleXMLElement $x){
	$rew = array();
	foreach ($x->children() as $key) {
		if($key->count() >0){
			$rew+=S_XML_EtoArray($key);
		}else{
			$klucz= "".$key->getName();
			$wart="".$key->__toString();
			//echo " name:".$klucz." value:".$wart." </br>";
			$rew[$klucz]=$wart;
		}
	}
	return $rew;
}
//ok
function HeadArray(array $x){
	$ret = array();
	foreach($x as $y){
		$ret += array_keys($y);
	}
	return array_unique($ret);
}

//ok
/**
 * generuje element Table na podstawie podanej tablicy
 * @param array $X_Arr
 */
function GenerujTabele(array $X_Arr = null){
	if($X_Arr == null)return; // takie drobne zabezpieczenie
	$head = HeadArray($X_Arr);
	echo "<table border='1'><thead id='head'><tr><th>L.p</th>";
	$head_2 = "<tr><th></th>";
	foreach($head as $key=>$value){
		echo "<th>".$value."</th>";
		$head_2.= "<th name='$value'><input name='$value' /></th>";
	}
	echo "</tr>".$head_2."</tr></thead><tbody id='body'>";
	$i =0;
	foreach($X_Arr as $tab){
		echo "<tr id='".$tab['pesel']."'><td>".++$i."</td>";
		foreach($head as $key=>$value){
			$tab_text = "";
			if(isset($tab[$value]))$tab_text = $tab[$value];
			echo "<td name='$value'>".$tab_text."</td>";
		}
		echo "</tr>";
		}
		echo "</tbody></table>";
}

?>