#!/usr/bin/php
<?php  
/**
 * Take on the csv reader 
 * not as efficient or simple as the streaming alternative - aimed for small files -
 * but there is an array at the end to play with, which is nice 
 * more use of classes
 */

class CsvToArrayReader extends SPLFileInfo implements Iterator, SeekableIterator{
	protected $map, $fp, $currentLine; 

	public function __construct( $filename, $mode = 'r', $use_include_path = false, $context = null ){
		parent::__construct( $filename );

		if( isset( $context ) ){
			$this->fp = fopen( $filename, $mode, $use_include_path, $context );
		}else{ 
			$this->fp = fopen( $filename, $mode, $use_include_path );
		}

		if( !$this->fp ){
			throw new Exception("Cannot read file", 1);			
		}

		// get the lines
		$this->map = $this->getMeLines();
		$this->currentLine = 0; 
	} // end constructor


	function getMeLines( $delimiter = ",", $enclosure = '"' ){
		return fgetcsv( $this->fp, 0, $delimiter, $enclosure ); 
	} // getMeLines

	function key(){
		return $this->currentLine;
	}

	function next(){
		$this->currentLine++;
		fgets( $this->fp );
	}

	function current(){
		$fpLoc = ftell( $this->fp );
		$data  = $this->getMeLines();
		fseek( $this->fp, $fpLoc );
		return array_combine( $this->map, $data );
	}

	function valid(){
		if( feof( $this->fp ) ){
			return false;
		}

		$fpLoc = ftell( $this->fp );
		$data  = $this->getMeLines();
		fseek( $this->fp, $fpLoc );
		return ( is_array( $data ) );
	}
 
	function rewind(){
		$this->currentLine = 0;
		fseek( $this->fp, 0 ); 
		fgets( $this->fp );
	}

	function seek( $line ){
		$this->rewind(); 
		while( $this->currentLine < $line && !$this->eof() ){
			$this->currentLine++;
			fgets( $this->fp );
		}
	}// seek 
}
 

$tmpArr = [];
foreach ( array_slice( $argv, 1 ) as $file ) {
	$it = new CsvToArrayReader( $file );	
	$filename = basename($file);
	$tmpArr[$filename] = iterator_to_array( $it, false ) ;		
}

foreach ( $tmpArr as $fileName => $fileCsv ) {
	echo "\"email_hash\",\"category\",\"filename\"\n";
	foreach ($fileCsv as $k => $value) {
		echo "\"{$value['email_hash']}\",\"{$value['category']}\",\"{$fileName}\"\n";
	}
}