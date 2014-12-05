#!/usr/bin/php
<?php
// Code by Andrew Samuels
// Please view README document for usage
// Questions? Please email andrewmsamuels@gmail.com

$fileName = $argv[1];
$command = $argv[2];
@$foo = $argv[3];

class  MyParser {

public function execute(){
	global $fileName, $command, $foo;
	if($command == "find-by-id"){
		$FindById = $this->FindByID($fileName, $foo);
		echo $FindById;
	}elseif($command == "find-all"){
		$FindAll = $this->FindAll($fileName);
	}elseif($command == "find-by-category"){
		$FindByCategory = $this->FindByCategory($fileName, $foo);
	}else{
		echo "Command not found. Please view README for accepted commands" . "\n";
	}
}//end execute

//Find items by id
protected function FindById($fileName, $id){
	$fileExt = explode(".", $fileName);
	if($fileExt[1] == "csv"){
		$csv = $this->csvFindById($fileName, $id);
		if(is_array($csv) && !empty($csv)){
			echo $csv[0];
			unset($csv[0]);
			foreach($csv as $cat){
				echo "- " . $cat . "\n";
			}
			echo "\n";
		}else {
			echo "No results found." . "\n";
		}
	}elseif($fileExt[1] == "nl"){
		// echo "Place nl resuls here." . "\n";
		$nl = $this->nlFindById($fileName, $id);
		if(is_array($nl) && !empty($nl)){
	                echo $nl[0];
	                unset($nl[0]);
	                foreach($nl as $cat){
	                        echo "- " . $cat . "\n";
	                }

                        echo "\n";
		}else{
                        echo "No results found." . "\n";
		}
		
	}elseif($fileExt[1] == "xml"){
		echo "XML is not supported yet." . "\n";
	}elseif($fileExt[1] == "json"){
		echo "JSON is not supported yet." . "\n";
	}else{
		echo "File type not found." . "\n";
	}

}//end FindById


// List all items
protected function FindAll($fileName){
	$fileExt = explode(".", $fileName);
	if ($fileExt[1] == "csv"){
			$csv = $this->csvFindAll($fileName);
	//echo $csv;
	}elseif ($fileExt[1] == "nl"){
			$nl = $this->nlFindAll($fileName);
	//echo $nl;
	}elseif($fileExt[1] == "xml"){
		echo "XML is not supported yet." . "\n";
	}elseif($fileExt[1] == "json"){
		echo "JSON is not supported yet." . "\n";
	}else{
		echo "File type not found." . "\n";
	}
}//end FindAll


//Find items by Category
protected function FindByCategory($fileName, $category){
	$fileExt = explode(".", $fileName);
	//parse the file
    if ($fileExt[1] == "csv"){
		$csv = $this->csvFindByCat($fileName, $category);
	}elseif ($fileExt[1] == "nl"){
                $nl = $this->nlFindByCat($fileName, $category);
	}elseif($fileExt[1] == "xml"){
		echo "XML is not supported yet." . "\n";
	}elseif($fileExt[1] == "json"){
		echo "JSON is not supported yet." . "\n";
	}else{
		echo "File type not found." . "\n";
	}
}//end FindByCategory


// Parse CSV files by id
protected function csvFindById($fileName, $id){
	if (($handle = fopen($fileName, "r")) !== FALSE){
		//get line from file pointer and parse for CSV fields
                while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
                        if($data[0] == $id && $this->valId($data[0]) && $this->valStrLen($data[1]) && 
$this->valCheckPosNum($data[2])){
                                $res1 = $data[0] . " " .  $data[1] . " " . "(" . $data[2] . ")" . "\n";
                                $res2 = explode(";", $data[3]);
                                array_unshift($res2, $res1);
                                $res = $res2;
                        }
                }
        }
        fclose($handle);
        if(empty($res)){
                return "No results found." . "\n";
        }else{
                return array_filter($res);
        }
}// end csvFindById


//Parse NL files by id
protected function nlFindById($fileName, $id){
	$txt = file_get_contents($fileName);
	$rows = explode("\n", $txt);
	//print_r($rows);
	array_filter($rows);
	$chunks = array_chunk($rows, 4);
	$key = array_search($id, $rows);
	foreach($chunks as $chunk){
	        if ($chunk[0] == $id && $this->valId($chunk[0]) && $this->valStrLen($chunk[1]) && $this->valCheckPosNum($chunk[2])){
	                $res1 = $chunk[0] . " " .  $chunk[1] . " " . "(" . $chunk[2] . ")" . "\n";
	                //echo $res1;
	                $res2 = explode(";", $chunk[3]);
	                array_unshift($res2, $res1);
	                $res = $res2;
			//return array_filter($res);
	              //  print_r($res);
	        }
	}
        if(empty($res)){
                return "No results found." . "\n";
        }else{
                return array_filter($res);
        }
}//end nlFindById

//Find all items in the CSV file
protected function csvFindAll($fileName){
	if (($handle = fopen($fileName, "r")) !== FALSE){
		//get line from file pointer and parse for CSV fields
    	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        	if($this->valId($data[0]) && $this->valStrLen($data[1]) && $this->valCheckPosNum($data[2])){
                	//$row++;
                	//i = 0;
                	$cats = explode(";", $data[3]);
                	$length = count($cats);
                	echo $data[0] . " " .  $data[1] . " " . "(" . $data[2] . ")" . "\n";
                	for($i=0;$i<$length;$i++){
                        	if (!empty($cats[$i])){
                                	echo "- " . $cats[$i] . "\n";
                        	}
                	}
			echo "\n";
        	}
    	}
	fclose($handle);
	}
}//end csvFindAll


//Find all items in the NL file 
protected function nlFindAll($fileName){
	$txt = file_get_contents($fileName);
	$rows = explode("\n", $txt);
	//print_r($rows);
	//delete blank lines
	array_filter($rows);
	$chunks = array_chunk($rows, 4);
	//print_r($chunks);
	foreach($chunks as $chunk){
		if($this->valId($chunk[0]) && $this->valStrLen($chunk[1]) && $this->valCheckPosNum($chunk[2])){
				echo $chunk[0] . " " .  $chunk[1] . " " . "(" . $chunk[2] . ")" . "\n";
				$cats = explode(";", $chunk[3]);
				$length = count($cats);
				for($i=0;$i<$length;$i++){
				if(!empty($cats[$i])){
							echo "- " . $cats[$i] . "\n";
				}
				}
			echo "\n";
		}
	}
}//end nlFindAll

//Parse CSV files by Category
protected function csvFindByCat($fileName, $category){

	if (($handle = fopen($fileName, "r")) !== FALSE){
		//get line from file pointer and parse for CSV fields
		while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
	        if ($this->valId($data[0]) && $this->valStrLen($data[1]) && $this->valCheckPosNum($data[2])){
	                //echo $data[3] . "\n";
	                $cats = preg_split('/[,;]/', $data[3]);
	                //print_r($cats);
	                foreach($cats as $cat){
	                        if($cat == $category){
	                                echo $data[0] . " " .  $data[1] . " " . "(" . $data[2] . ")" . "\n";
	                                foreach($cats as $c){
	                                        if(!empty($c)){
	                                                echo "- ". $c . "\n";
	                                        }
	                                }
					echo "\n";
	                        }
 	               }
		}
		}//end while
		fclose($handle);
	}


}//end csvFindByCat

//Parse NL files by Category
protected function nlFindByCat($fileName, $category){
	$txt = file_get_contents($fileName);
	$rows = explode("\n", $txt);
	//delete blank lines
	array_filter($rows);
	$chunks = array_chunk($rows, 4);
	foreach($chunks as $chunk){
		if($this->valId($chunk[0]) && $this->valStrLen($chunk[1]) && $this->valCheckPosNum($chunk[2])){
			$cats = preg_split('/[,;]/', $chunk[3]);
			//print_r($cats);
			foreach($cats as $cat){
					if($cat == $category){
					echo $chunk[0] . " " .  $chunk[1] . " " . "(" . $chunk[2] . ")" . "\n";
											foreach($cats as $c){
													if(!empty($c)){
															echo "- ". $c . "\n";
													}
											}
											echo "\n";
					}
			}
		}
	}
}//end nlFindByCat

//XML parsers
protected function xmlFindById(){

}

protected function xmlFindAll(){

}

protected function xmlFindByCat(){

}

//JSON parsers
protected function jsonFindById(){

}

protected function jsonFindAll(){

}

protected function jsonFindByCat(){

}


//MSSQL connection
protected function msSQLconnect($server, $username, $password, $dbName){
	$link = mssql_connect($server, $username, $password);

	if (!$link) {
		die('Could not connect to MSSQL');
	}
}

//MySQL Connection
protected function mySQLconnect($server, $username, $password, $dbName){
	$dbConnect = mysql_connect($server, $username, $password);
	//Code here
	if (!$dbConnect) {
		die('Could not connect to MySQL');
	}
}


//Validate quantity as a positive non-zero whole number
protected function valCheckPosNum ($numStr){
	if (is_numeric($numStr) && strpos($numStr, ".")){
		return false;
	}else{
			//check quantity for positive number
			$num = (int)$numStr;
			if ((int)$num == $num && (int)$num > 0 ){
					return true;
			}else{
					return false;
			}
	}
}

//Validate string
protected function valStrLen ($str){
		//check name for at least 1 char
		if (strlen($str) > 0 ){
				return true;
		}else{
				return false;
		}
}

//Validate the id
protected function valId ($id){
		//check for valid id
		if (preg_match("/[0-9]{2}-[a-zA-Z]{2}-[a-zA-Z0-9]{4}/", $id)){
				return true;
		}else{
				return false;
		}
}



}//end MyParser

$mp = new MyParser();
$mp->execute();

?>
