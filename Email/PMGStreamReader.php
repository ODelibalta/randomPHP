#!/usr/bin/php
<?php
 
if( $argc < 2 ){
	die( "Please send the csv file path arguments" );
} else{
	foreach ( array_slice( $argv, 1) as $file ) {
		$it = new MyObjectiveReader( $file ); 
	}
} 

/**
 * Simple take on the CSV reader focused on performance
 * Tested on files close to 3GB each
 */
class PMGStreamReader{ 
	
	protected $fh, $lineCounter; 

	public function __construct( $filename, $use_include_path = false, $context = null ){
		
		$this->lineCounter = 0;

		if( isset( $context ) ){
			$this->fh = fopen( $filename, "r", $use_include_path, $context );
		}else{ 
			$this->fh = fopen( $filename, "r", $use_include_path );
		}

		if( !$this->fh ){
			throw new Exception("Cannot read file", 1);			
		}

		// get the lines
		$this->getMeLines(); 

		fclose( $this->fh );
	} // end constructor

	// return the current line on the pointer concatenated with the current file name 
	function getMeLines(){
		if ( $this->fh ) {			
			// get current file name
			$meta = stream_get_meta_data( $this->fh );
			$currFileName = basename( $meta["uri"] );
			
    		while ( ( $buffer = fgets( $this->fh, 4096 ) ) !== false) {
    			if( ! $this->lineCounter ){
    				echo str_replace( "\n", ",",$buffer )."\"filename\"\n";    				
				} else{
	    			// the current line with the file name as if part of the csv file
	    			echo str_replace( "\n", "",$buffer ).",\"{$currFileName}\"\n";
				}

    			++$this->lineCounter;
		    }

		    if ( !feof( $this->fh ) ) {
		        echo "Error: unexpected fgets() fail\n";
		    } 
		}
	} // getMeLines
} // end class 
