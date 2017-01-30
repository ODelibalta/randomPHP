#!/usr/bin/php
<?php 
/**
* Usage: php CsvReaderThing.php
* 	Should print CSV files under ./data directory or let you specify a directory otherwise
*   Use for whatever you like. Meant to be a simple script - sorry for the lack of comments / polishing etc. 
*   I wrote it because I was feeling down ( not having a good day: a mosque burned down today )
*/

require 'ConsoleTable.php';
use LucidFrame\Console\ConsoleTable;
 
class CsvReaderThing{
 	private $dataDir = "";
 	private $files   = []; 
 	private $selectedFile = ""; 
 
	// ask if should look for data folder or needs specified
	function __construct(){
		$this->logInfo( "Looking for the data folder." );
		// set the folder location for the csv files
		$this->setDataDir();
		// see if we have any good csv files within it
	 	$this->setCSVFiles(); 
	 	// we have good files, which one you want to see 
 		$this->setSelectedFile(); 
 		// print the selected one
 		$this->printFile();
	}

	function setSelectedFile(){

		$this->logInfo( "Please type a file number (or type: all) to print the file: " );
		
		foreach ( $this->files as $key => $file ) {
		 	echo "[".$key."] ".basename( $file )."\n";
		}	
		
		$this->selectedFile = $this->getInput();

		if( ! $this->files[$this->selectedFile] && $this->selectedFile !== "all" ){
			echo "not in here";
			$this->setSelectedFile();
		} 

	}

	function printFile(){
		$filesToPrint = [];

		if( $this->selectedFile === "all" ){
			foreach ($this->files as $dirFile ) {
				$filesToPrint[] = $dirFile; 
			}
		} else {
			$filesToPrint[] = $this->files[$this->selectedFile];			
		}

		// read in the data 
		foreach( $filesToPrint as $file ) {
			var_dump( $file );
			// open files
			if( ( $handle = fopen( "{$file}", "r" ) ) !== FALSE ) { // we checked this before but would not hurt
			    while ( ( $fileContent = fgetcsv( $handle, 1, "," ) ) !== FALSE ) {
			    	$data[$file][] = $fileContent; // create data array containing csv contents
			    }
			    fclose($handle);
			}
		} // end foreach( $files as $file )

		//Now process the data and create table
		$table = new ConsoleTable(); 
		foreach ( $data as $file => $content ) {
			// //Create the header part  first row is the header
			$table->setHeaders( $data[$file][0]  );
			$table->addHeader( "filename" );

			for($i=1; $i<count( $data[$file] ); $i++ ){ 
				$cell= $data[$file][$i]; 
				$cell[] = basename( $file );
				$table->addRow( $cell ); 
			} 
		} 
		$table->display();  
	}


	function setCSVFiles(){ 
		
		$tmpCSVFiles = glob( $this->dataDir."/*.csv");
		 
		if( empty( $tmpCSVFiles ) ) {
			$tryAgain = $this->getInput( $this->logWarning( "Could not find CSV files under given directory. Would you like to try again ?\n[1] Yes \n[0] No(Exits)" ) );
			
			if( $tryAgain === "0" ){
				exit();
			} else if ( $tryAgain === "1" ) { 
				$this->setDataDir( true );  
				$this->setCSVFiles();
			}  else {
				$this->setCSVFiles();
			}
			 
		}

		foreach ( $tmpCSVFiles as $file ) {
		 	if( is_readable( $file ) ){
		 		$this->files[] = $file;
		 	}
		}
 	
 		if( empty( $this->files ) ){
			$this->logWarning( "There does not seem to be any file(s) there that I can work with. Please come back when files are set properly.");
			exit();
		} 
	}


	function setDataDir( $manual = false ){
		// does the data folder exists
		if( $manual ){
			$this->dataDir = $this->getInput( $this->logWarning( "Please sepecify a location for csv files:" ) );	
		}else if( ! file_exists( "./data" ) ){
			$this->dataDir = $this->getInput( $this->logWarning( "Data folder is not found. Please sepecify a location:" ) );	
		}else{
			$this->dataDir = realpath( "./data" );
		}

		if( ! file_exists( $this->dataDir ) ){
			$this->setDataDir();
		} else {
			$this->logInfo( "Data location is set to ".$this->dataDir );		 	
		}
		
	}
  

	function getInput( $prompt = null ){
		if (PHP_OS == 'WINNT') {
			echo $prompt;
		  	$line = stream_get_line(STDIN, 1024, PHP_EOL);
		} else {
		  	$line = readline( $prompt );
		}
		return $line;
	}

	function logInfo( $str ){
		echo "\033[1;34mINF :\033[0m {$str}\n";
	} 
	function logWarning( $str ){
		echo "\033[1;33mWARN:\033[0m {$str}\n";		
	}
	function logError( $str ){
		echo "\033[1;31mERR:\033[0m {$str}\n";	
	}
	

}

new CsvReaderThing();  
