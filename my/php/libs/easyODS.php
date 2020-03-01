<?php 

//This PHP class must not be used in comercial projects or for selling.
//Any projects that will use this class must be open source and free of charge
//Any portion of the code is for educational propose only or for free projects.
//The person who made this class is the "OVIDIU MIHALAS - LESEI" a freelancer
//Contact me at hide@address.com or hide@address.com
//Yahoo messenger ID is " miledino "
//This class is open source. And must remain open source.
//
//Thank you for using my code. I hope you will enjoy it. 

Class easy_ods_read
{
	/**
	* Rows counter we start from the first row
	* @var Integer $rows
	*/
	private $rows=1;
	/**
	* Cell / Column counter we used the letter "A" to be the same as in Open Office 
	* Spreadsheet editor 
	* @var String $cell
	*/
	private $cell="A";
	/**
	* Current number of the sheet default is 0 (First sheet)
	* @var Integer $table
	*/
	private $table = 0;
	/**
	* Data from open office spreadsheet document 
	* @var Array $rows_data 
	*/
	private $rows_data = array();
	/**
	* Number of the sheet defined by user (Default is 0)
	* @var Integer $table
	*/
	private $spreadsheet = 0;	
	/**
	* Temporary number number of repeated columns 
	*/
	private $columns_repeated = 0;
	/**
	* Temporary number number of spanned columns 
	*/
	private $columns_spanned = 0;
	/**
	* If enabled the class will show more information on the captured values of cells
	*/
	private $debug = FALSE;
	/*
	* Used in End elemtn function
	*/
	private $flag_cell_increment = TRUE;	
	/**
	* Path to temporary directory
	*/
	public $store_dir = "";
	/**
	* Object constructor 
	* 
	* @param Integer $spreadsheet Number of spreadsheet starting from 0
	* @param String $file Content.xml file path. This file is stored in *.ods zip arhive 
	*/
	function __construct($spreadsheet=0,$file)
	{
		$this->spreadsheet = $spreadsheet;
		/**
		* Initialization of XML Parser
		* 
		* @var Resource $xml_parser XML Parser
		*/
		$xml_parser = xml_parser_create();		
		/**
		* The method that will take care of the XML data 
		*/
		xml_set_character_data_handler($xml_parser, "character_data");
		/**
		* The method that will take care of the XML data elements
		* Example : Rows or Columns etc...
		*/
		xml_set_element_handler($xml_parser, "start_element", "end_element");
		/**
		* We set the parse to be used inside an object
		*/
		xml_set_object ( $xml_parser, $this );
		/**
		* If we can not open the file we close the script with this error message
		*/
		if (!($fp = fopen($file, "r"))) 
		{
			die("could not open XML input");
		}
		/**
		* We store the temporary directory path to be 
		* used later for deletion
		*/
		$this->store_dir = preg_replace("/content.xml/","",$file);
		/**
		* Read every line 4096 Bytes (4MB) in lenght
		*/
		while ($data = fread($fp, 4096))
		{
			/**
			* In case of an error we close the script with this error message
			*/
		    if (!xml_parse($xml_parser, $data, feof($fp))) 
		    {
		        die(sprintf("XML error: %s at line %d",
		                    xml_error_string(xml_get_error_code($xml_parser)),
		                    xml_get_current_line_number($xml_parser)));
		    }
		}
		/**
		* Free our memory
		*/
		xml_parser_free($xml_parser);		
	}
	/**
	* This method will check if there are empty rows in document and
	* if there are then will add the empty rows number to $this->rows
	* 
	* The data resulting from this function is used for makeing the data array
	* 
	* @param Resource $parser XML Parse resource
	* @param String $name XML Tag name
	* @param Array $attrs XML Tag atributes
	*/
	private function start_element($parser, $name, $attrs) 
	{
		/**
		* If this table is equal to the spreadsheet defined
		*/
		if($this->table == $this->spreadsheet)
		{
			/**
			* XML Tag name is "TABLE:TABLE-ROW"
			*/			
			if($name == "TABLE:TABLE-ROW")			
			{	/**
				* If there are empty rows
				*/
				if(isset($attrs['TABLE:NUMBER-ROWS-REPEATED']))
				{
					/**
					* We add these empty rows to the row counter
					* @var Integer $this->rows Rows counter
					*/
					$this->rows = ($this->rows-1) + $attrs['TABLE:NUMBER-ROWS-REPEATED'];				
				}				
			}
			/**
			* XML Tag name is "TABLE:TABLE-CELL"
			*/			
			if($name == "TABLE:TABLE-CELL")
			{	
				if(isset($attrs['TABLE:NUMBER-COLUMNS-REPEATED']) && $attrs['TABLE:NUMBER-COLUMNS-REPEATED']!==0 && isset($attrs['OFFICE:VALUE-TYPE']))
				{					
					$this->columns_repeated = $attrs['TABLE:NUMBER-COLUMNS-REPEATED'];
				}
				if(isset($attrs['TABLE:NUMBER-COLUMNS-SPANNED']) && $attrs['TABLE:NUMBER-COLUMNS-SPANNED']!==0 && isset($attrs['OFFICE:VALUE-TYPE']))
				{						
					$this->columns_spanned = $attrs['TABLE:NUMBER-COLUMNS-SPANNED'];
				}			
				/**
				* If there is and empty cell
				*/
				if(
					!isset($attrs['OFFICE:VALUE']) && 
					!isset($attrs['OFFICE:VALUE-TYPE'])
				  )
				{
					if(isset($attrs['TABLE:NUMBER-COLUMNS-REPEATED']) && $attrs['TABLE:NUMBER-COLUMNS-REPEATED'] > 0)
					{
						$this->columns_repeated = $attrs['TABLE:NUMBER-COLUMNS-REPEATED'];
					}
					if(isset($attrs['TABLE:NUMBER-COLUMNS-SPANNED']) && $attrs['TABLE:NUMBER-COLUMNS-SPANNED'] > 0)
					{					
						$this->columns_spanned = $attrs['TABLE:NUMBER-COLUMNS-SPANNED'];
					}
					$this->character_data();
					return;
				}
			}
		}
	}
	/**
	* This method will count the spreadsheet / cell / row numbers 
	* The data resulting from this function is used for makeing the data array
	* 
	* @param Resource $parser XML Parse resource
	* @param String $name XML Tag name
	*/
	private function end_element($parser, $name) 
	{
		/**
		* If this table is equal to the spreadsheet defined
		*/		
		if($this->table == $this->spreadsheet)
		{
			/**
			* XML Tag name is "TABLE:TABLE-ROW"
			*/
			if($name == "TABLE:TABLE-ROW")
			{	
				++$this->rows;
				$this->cell = "A";
			}
			/**
			* XML Tag name is "TABLE:TABLE-CELL"
			*/			
			if($name == "TABLE:TABLE-CELL")
			{	
				if($this->flag_cell_increment == TRUE)
				{
					++$this->cell;
				}
				if($this->columns_spanned > 1)
				{
					for($i=1; $i<$this->columns_spanned; $i++)
					{
						if($this->debug == TRUE)
						{
							$this->rows_data[$this->rows][++$this->cell] = "SPANNED VALUE";							
						}
						else
						{
							++$this->cell;
						}
					}	
					$this->cell++;
					$this->columns_spanned = 0;							
				}
			}			
		}
		/**
		* XML Tag name is "TABLE:TABLE"
		*/				
		if($name == "TABLE:TABLE")
		{		
			++$this->table; 
		}	
	}
	/**
	* This method is used to extract data from XML
	* 
	* @param Integer $start_row Number of row as it is in Open Office Spreadsheet
	* @param Integer $end_row Number of row as it is in Open Office Spreadsheet
	*
	*/
	public function extract_data($start_row,$end_row)
	{
		/**
		* We call this function here to grab the value of
		* the last cell in the last column of the last line 
		* @param int 1 is the flag that tells the function that we are at the last line
		*/
		
		for($i=$start_row;$i<=$end_row;$i++)
		{				
			/**
			* If the $start_row exists
			*/	
			if(isset($this->rows_data[$i]))
			{
				$data_extracted[$i] = $this->rows_data[$i];
			}
		}
		if(isset($data_extracted))
		{
			return $data_extracted;
		}
		else
			return NULL;
		
	}
	/**
	* This method is used to store the XML data in $this->rows_data
	* 
	* @param Resource $parser XML Parse resource
	* @param String / Integer $data XML Values
	*/
	private function character_data($parser = NULL, $data = NULL)
	{
		if($this->table == $this->spreadsheet)
		{					
			if($this->columns_repeated > 0 && $data !== NULL)
			{
				for($i=0; $i<$this->columns_repeated; $i++)
				{
					$this->rows_data[$this->rows][$this->cell++] = $data;
				}
				$this->columns_repeated = 0;
				$this->flag_cell_increment = FALSE;
				return;
			}
			elseif($this->columns_spanned > 1 && $data !== NULL)
			{
				if("ASCII" !== mb_detect_encoding($data))
				{
					if(!isset($this->rows_data[$this->rows][$this->cell]))
					{
						$this->rows_data[$this->rows][$this->cell] = "";
					}
					$this->rows_data[$this->rows][$this->cell] .= $data;	
					$this->flag_cell_increment = FALSE;
					return;				
				}
				else
				{
					if(isset($this->rows_data[$this->rows][$this->cell]))
					{
						$this->rows_data[$this->rows][$this->cell] .= $data;
					}
					else
					{
						$this->rows_data[$this->rows][$this->cell] = $data;						
					}
					$this->flag_cell_increment = FALSE;
					return;				
				}
			}
			elseif($data !== NULL)
			{	
				//Do not change this , becaouse is very usefull when this function is called 1 or more times
				//and we are on the same cell 
				//This function is called 1 ore more times if there are charaters in the XML that are non ASCII standard.
				if(!isset($this->rows_data[$this->rows][$this->cell]))
				{
					$this->rows_data[$this->rows][$this->cell] = "";
				}
				$this->rows_data[$this->rows][$this->cell] .= ltrim($data,"-");
				$this->flag_cell_increment = TRUE;
				return;								
				
			}
			elseif($data == NULL)
			{
				if($this->columns_repeated > 0)
				{
					for($i=0; $i<$this->columns_repeated; $i++)
					{
						if($this->debug == TRUE)
						{						
							$this->rows_data[$this->rows][$this->cell++] = "NULL REPEATED";
						}
						else
						{
							$this->cell++;
						}
					}
					$this->flag_cell_increment = FALSE;
				}
				if($this->columns_spanned > 0)
				{
					for($i=0; $i<$this->columns_spanned; $i++)
					{
						if($this->debug == TRUE)
						{
							$this->rows_data[$this->rows][$this->cell++] = "NULL SPANNED";						
						}
						else
						{
							$this->cell++;
						}						
					}
					$this->flag_cell_increment = FALSE;
				}				
				if($this->columns_spanned == 0 && $this->columns_repeated == 0)
				{
					if($this->debug == TRUE)				
					{
						$this->rows_data[$this->rows][$this->cell] = "NULL EMPTY";
					}
					$this->flag_cell_increment = TRUE;							
				}	
				$this->columns_spanned = 0;
				$this->columns_repeated = 0;
			}
		}		
	}
	/**
	* This method will extract the content.xml from the zip arhive of 
	* Open document spreadsheet file
	* 
	* @param String $file Open Document spreadsheet file
	* @param String $store_dir The dir where we wanna to extract the file content.xml
	* 
	* @return String Content.xml file path
	*/
	static function extract_content_xml($file,$store_dir)
	{
		if(!is_dir($store_dir))
		{
			mkdir($store_dir);
		}
		copy($file,$store_dir.'/'.basename($file).".zip");
		$path = $store_dir.'/'.basename($file).".zip";
		$uid = uniqid();
		mkdir($store_dir.'/'.$uid);
		$zip = new ZipArchive;
		if ($zip->open($path) === TRUE)
		{
			$zip->extractTo($store_dir.'/'.$uid);
			$zip->close();
		}
		unlink($path);
		return $store_dir.'/'.$uid.'/content.xml';
	}
	
	/**
	* This code has been taken from "lixlpixel.org" at this address
	* http://lixlpixel.org/recursive_function/php/recursive_directory_delete/
	*/
	public function delete_temporary_directory($directory, $empty=FALSE)
	{
		//if the path has a slash at the end we remove it here
		if(substr($directory,-1) == '/')
		{
			$directory = substr($directory,0,-1);
		}  
		//if the path is not valid or is not a directory ...
		if(!file_exists($directory) || !is_dir($directory))
		{
			//we return false and exit the function
			return FALSE;	  
		//if the path is not readable
		}
		elseif(!is_readable($directory))
		{
			//we return false and exit the function
			return FALSE;
	  
		//else if the path is readable
		}
		else
		{
			//we open the directory
			$handle = opendir($directory);
	  
			//and scan through the items inside
			while (FALSE !== ($item = readdir($handle)))
			{
				//if the filepointer is not the current directory
				//or the parent directory
				if($item != '.' && $item != '..')
				{
					//we build the new path to delete
					$path = $directory.'/'.$item;
	  
					//if the new path is a directory
					if(is_dir($path)) 
					{
						//we call this function with the new path
						$this->delete_temporary_directory($path);
	  
					 //if the new path is a file
					}
					else
					{
						//we remove the file
						unlink($path);
					}
				}
			}
			//close the directory
			closedir($handle);
	  
			 //if the option to empty is not set to true
			if($empty == FALSE)
			{
				//try to delete the now empty directory
				if(!rmdir($directory))
				{
					//return false if not possible
					return FALSE;
				}
			}
			//return success
			return TRUE;
		}
	}
}
?>