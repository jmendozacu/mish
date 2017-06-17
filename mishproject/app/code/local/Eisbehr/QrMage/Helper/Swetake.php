<?php
// Information:
// Swetake QrCode Engine

class Eisbehr_QrMage_Helper_Swetake extends Mage_Core_Helper_Abstract
{
	/* class config */
	
	private $data_path;
	private $image_path;
	private $version_ul;
	
	/* user define */
	
	private $qrcode_data_string;
	private $qrcode_error_correct;
	private $qrcode_module_size;
	private $qrcode_version;
	private $qrcode_image_type;
	
	/* structured append (experimental) */
	
	private $qrcode_structureappend_n;
	private $qrcode_structureappend_m;
	private $qrcode_structureappend_parity;
	private $qrcode_structureappend_originaldata;
	
	/* internal */
	
	private $data_length;
	private $data_counter;
	private $data_value;
	private $data_bits;
	private $total_data_bits;
	private $max_data_bits;
	private $max_data_codewords;
	private $codewords;
	private $codewords_counter;
	private $max_codewords;
	private $codeword_num_plus;
	private $codeword_num_counter_value;
	private $error_correct_mode;
	private $rs_block_order;
	private $rs_ecc_codewords;
	private $rs_cal_table_array;
	private $max_modules_1side;
	private $matrix_content;
	private $matrix_x_array;
	private $matrix_y_array;
	private $mask_array;
	private $mask_number;
	private $mask_content;
	private $byte_num;
	private $format_information_x2;
	private $format_information_y2;
	private $frame_data;
	
	/* data arrays */
	
	private $alphanumeric_character_hash;
	private $ecc_character_hash;
	private $max_data_bits_array;
	private $max_codewords_array;
	private $matrix_remain_bit;
	private $format_information_x1;
	private $format_information_y1;
	private $format_information_array;
	
	/* image */
	
	private $image_data;
	private $output_image;
	private $base_image;
	private $qrcode_image_size;
	private $mib;
	
	/* construct */
	
	function __construct()
	{
		return $this->cleanup();
	}
	
	/* destruct */
	
	function __destruct()
	{
		return $this->cleanup();
	}
	
	/* create */
	
	public function createQrCode()
	{
		if( !$this->checkQrCodeConfig() )
		{
			return false;
		}
		
		/* cleanup */
		
		$this->cleanup();
		
		/* data arrays */
		
		$this->createDataArrays();
		
		/* check */
		
		$this->checkQrCodeImageType();
		$this->checkQrCodeModuleSize();
		$this->checkQrCodeDataString();
		$this->checkQrCodeStructureAppend();
		
		/* determine */
		
		$this->determineEncodeMode();
		$this->determineEccMode();
		$this->determineQrCodeVersion();
		$this->determineTerminator();
		
		/* method */
		
		$this->divideDataEightBit();
		$this->paddingCharacter();
		$this->errorCorrection();
		$this->flashMatrix();
		$this->attachData();
		$this->maskSelect();
		$this->formatInformation();
		$this->fillDataArray();
		
		/* image */
		
		$this->drawImage();
		$this->outputImage();
		
		/* cleanup */
		
		$this->cleanup();
		
		return $this;
	}
	
	/* setter */
	
	public function setConfigDataPath($value)
	{
		$this->data_path = $value;
		return $this;
	}
	
	public function setConfigImagePath($value)
	{
		$this->image_path = $value;
		return $this;
	}
	
	public function setConfigVersion($value)
	{
		$this->version_ul = $value;
		return $this;
	}
	
	public function setQrCodeDataString($value)
	{
		$this->qrcode_data_string = @$value;
		return $this;
	}
	
	public function setQrCodeErrorCorrect($value)
	{
		$value  = strtoupper($value);
		$values = array('L', 'M', 'Q', 'H');
		
		if( in_array($value, $values) )
		{
			$this->qrcode_error_correct = @$value;
			return $this;
		}
		
		return false;
	}
	
	public function setQrCodeModuleSize($value)
	{
		$this->qrcode_module_size = @$value;
		return $this;
	}
	
	public function setQrCodeVersion($value)
	{
		$this->qrcode_version = @$value;
		return $this;
	}
	
	public function setQrCodeImageType($value)
	{
		$this->qrcode_image_type = @$value;
		return $this;
	}
	
	public function setQrCodeStructureAppendN($value)
	{
		$this->qrcode_structureappend_n = @$value;
		return $this;
	}
	
	public function setQrCodeStructureAppendM($value)
	{
		$this->qrcode_structureappend_m = @$value;
		return $this;
	}
	
	public function setQrCodeStructureAppendParity($value)
	{
		$this->qrcode_structureappend_parity = @$value;
		return $this;
	}
	
	public function setQrCodeStructureAppendOriginalData($value)
	{
		$this->qrcode_structureappend_originaldata = @$value;
		return $this;
	}
	
	/* getter */
	
	public function getConfigDataPath($value)
	{
		return $this->data_path;
	}
	
	public function getConfigImagePath($value)
	{
		return $this->image_path;
	}
	
	public function getConfigVersion()
	{
		return $this->version_ul;
	}
	
	public function getQrCodeDataString()
	{
		return $this->qrcode_data_string;
	}
	
	public function getQrCodeErrorCorrect()
	{
		return $this->qrcode_error_correct;
	}
	
	public function getQrCodeModuleSize()
	{
		return $this->qrcode_module_size;
	}
	
	public function getQrCodeVersion()
	{
		return $this->qrcode_version;
	}
	
	public function getQrCodeImageType()
	{
		return $this->qrcode_image_type;
	}
	
	public function getQrCodeStructureAppendN()
	{
		return $this->qrcode_structureappend_n;
	}
	
	public function getQrCodeStructureAppendM()
	{
		return $this->qrcode_structureappend_m;
	}
	
	public function getQrCodeStructureAppendParity()
	{
		return $this->qrcode_structureappend_parity;
	}
	
	public function getQrCodeStructureAppendOriginalData()
	{
		return $this->qrcode_structureappend_originaldata;
	}
	
	/* data arrays */
	
	public function createDataArrays()
	{
		$this->alphanumeric_character_hash	= array("0"=>0,  "1"=>1,  "2"=>2,  "3"=>3,  "4"=>4,  "5"=>5,  "6"=>6,  "7"=>7,  
													"8"=>8,  "9"=>9,  "A"=>10, "B"=>11, "C"=>12, "D"=>13, "E"=>14, "F"=>15, 
													"G"=>16, "H"=>17, "I"=>18, "J"=>19, "K"=>20, "L"=>21, "M"=>22, "N"=>23, 
													"O"=>24, "P"=>25, "Q"=>26, "R"=>27, "S"=>28, "T"=>29, "U"=>30, "V"=>31, 
													"W"=>32, "X"=>33, "Y"=>34, "Z"=>35, " "=>36, "$"=>37, "%"=>38, "*"=>39, 
													"+"=>40, "-"=>41, "."=>42, "/"=>43, ":"=>44);
		
		$this->ecc_character_hash 			= array("L" => "1", 
													"M" => "0", 
													"Q" => "3", 
													"H" => "2");
		
		$this->max_data_bits_array 			= array(0,     128,   224,   352,   512,   688,   864,   992,   1232,  1456, 1728,  
													2032,  2320,  2672,  2920,  3320,  3624,  4056,  4504,  5016,  5352,  
													5712,  6256,  6880,  7312,  8000,  8496,  9024,  9544,  10136, 10984,  
													11640, 12328, 13048, 13800, 14496, 15312, 15936, 16816, 17728, 18672,  
													152,   272,   440,   640,   864,   1088,  1248,  1552,  1856,  2192,  
													2592,  2960,  3424,  3688,  4184,  4712,  5176,  5768,  6360,  6888,  
													7456,  8048,  8752,  9392,  10208, 10960, 11744, 12248, 13048, 13880,  
													14744, 15640, 16568, 17528, 18448, 19472, 20528, 21616, 22496, 23648,  
													72,    128,   208,   288,   368,   480,   528,   688,   800,   976,  
													1120,  1264,  1440,  1576,  1784,  2024,  2264,  2504,  2728,  3080,  
													3248,  3536,  3712,  4112,  4304,  4768,  5024,  5288,  5608,  5960,  
													6344,  6760,  7208,  7688,  7888,  8432,  8768,  9136,  9776,  10208,  
													104,   176,   272,   384,   496,   608,   704,   880,   1056,  1232,  
													1440,  1648,  1952,  2088,  2360,  2600,  2936,  3176,  3560,  3880,  
													4096,  4544,  4912,  5312,  5744,  6032,  6464,  6968,  7288,  7880,  
													8264,  8920,  9368,  9848,  10288, 10832, 11408, 12016, 12656, 13328);
		
		$this->max_codewords_array 			= array(0,    26,   44,   70,   100,  134,  172,  196,  242,  292,  346,  404,  
													466,  532,  581,  655,  733,  815,  901,  991,  1085, 1156, 1258, 1364, 
													1474, 1588, 1706, 1828, 1921, 2051, 2185, 2323, 2465, 2611, 2761, 2876, 
													3034, 3196, 3362, 3532, 3706);
		
		$this->matrix_remain_bit 			= array(0, 0, 7, 7, 7, 7, 7, 0, 0, 0, 0, 0, 0, 0, 3, 3, 3, 3, 3, 3, 3, 
													4, 4, 4, 4, 4, 4, 4, 3, 3, 3, 3, 3, 3, 3, 0, 0, 0, 0, 0, 0);
		
		$this->format_information_x1 		= array(0, 1, 2, 3, 4, 5, 7, 8, 8, 8, 8, 8, 8, 8, 8);
		
		$this->format_information_y1 		= array(8, 8, 8, 8, 8, 8, 8, 8, 7, 5, 4, 3, 2, 1, 0);
		
		$this->format_information_array 	= array("101010000010010", "101000100100101", "101111001111100", "101101101001011",
													"100010111111001", "100000011001110", "100111110010111", "100101010100000", 
													"111011111000100", "111001011110011", "111110110101010", "111100010011101", 
													"110011000101111", "110001100011000", "110110001000001", "110100101110110", 
													"001011010001001", "001001110111110", "001110011100111", "001100111010000", 
													"000011101100010", "000001001010101", "000110100001100", "000100000111011", 
													"011010101011111", "011000001101000", "011111100110001", "011101000000110", 
													"010010010110100", "010000110000011", "010111011011010", "010101111101101");
		
		return $this;
	}
	
	/* check */
	
	private function checkQrCodeConfig()
	{
		/* old */
		/* if( empty($this->data_path) || empty($this->image_path) ) */
		if( empty($this->data_path) )
		{
			return false;
		}
		
		if( empty($this->version_ul) )
		{
			$this->setConfigVersion("40");
		}
		
		return true;
	}
	
	private function checkQrCodeImageType()
	{
		if ( $this->qrcode_image_type == "J" || $this->qrcode_image_type == "j" )
		{
			$this->qrcode_image_type = "jpeg";
		}
		else 
		{
			$this->qrcode_image_type = "png";
		}
		
		return $this;
	}
	
	private function checkQrCodeModuleSize()
	{
		if ( $this->qrcode_module_size <= 0 ) 
		{
			if ( $this->qrcode_image_type == "jpeg" )
			{
				$this->qrcode_module_size = 8;
			}
			else 
			{
				$this->qrcode_module_size = 4;
			}
		}
		
		return $this;
	}
	
	private function checkQrCodeDataString()
	{
		$this->qrcode_data_string = rawurldecode($this->qrcode_data_string);
		$this->data_length = strlen($this->qrcode_data_string);
		
		if ( $this->data_length <= 0 ) 
		{
			trigger_error("Swetake QR Code: No Data.", E_USER_ERROR);
			exit;
		}
		
		$this->data_counter = 0;
		return $this;
	}
	
	private function checkQrCodeStructureAppend()
	{
		if ( $this->qrcode_structureappend_n > 1 && $this->qrcode_structureappend_n <= 16
			 && $this->qrcode_structureappend_m > 0 && $this->qrcode_structureqppend_m <= 16 )
		{
			$this->data_value[0] = 3;
			$this->data_bits[0]  = 4;
		
			$this->data_value[1] = $this->qrcode_structureappend_m - 1;
			$this->data_bits[1]  = 4;
		
			$this->data_value[2] = $this->qrcode_structureappend_n - 1;
			$this->data_bits[2]  = 4;
			
			$originaldata_length = strlen($this->qrcode_structureappend_originaldata);
			
			if ( $originaldata_length > 1 )
			{
				$this->qrcode_structureappend_parity = 0;
				
				for( $i = 0; $i < $originaldata_length; $i++ )
				{
					$this->qrcode_structureappend_parity = ($this->qrcode_structureappend_parity ^ ord(substr($this->qrcode_structureappend_originaldata, $i, 1)));
				}
			}
		
			$this->data_value[3] = $this->qrcode_structureappend_parity;
			$this->data_bits[3]  = 8;
		
			$this->data_counter  = 4;
		}
		
		$this->data_bits[$this->data_counter] = 4;
		return $this;
	}
	
	/* determine */
	
	private function determineEncodeMode()
	{
		if ( preg_match("/[^0-9]/", $this->qrcode_data_string) != 0 )
		{
    		if ( preg_match("/[^0-9A-Z \$\*\%\+\.\/\:\-]/", $this->qrcode_data_string) != 0 )
			{
        		$this->codeword_num_plus = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 
												 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 
												 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8);

				$this->data_value[$this->data_counter] = 4;
				$this->data_counter++;
				$this->data_value[$this->data_counter] = $this->data_length;
				$this->data_bits[$this->data_counter]  = 8;
				$this->codeword_num_counter_value      = $this->data_counter;
				$this->data_counter++;
		
				for($i = 0; $i < $this->data_length; $i++)
				{
					$this->data_value[$this->data_counter] = ord(substr($this->qrcode_data_string, $i, 1));
					$this->data_bits[$this->data_counter]  = 8;
					$this->data_counter++;
				}
			} 
			else 
			{
				$this->codeword_num_plus = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 
												 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 
												 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4);

				$this->data_value[$this->data_counter] = 2;
				$this->data_counter++;
				$this->data_value[$this->data_counter] = $this->data_length;
				$this->data_bits[$this->data_counter]  = 9;
				$this->codeword_num_counter_value      = $this->data_counter;
		
				$this->data_counter++;
				
				for($i = 0; $i < $this->data_length; $i++)
				{
					if ( ($i %2) == 0 )
					{
						$this->data_value[$data_counter] = $alphanumeric_character_hash[substr($this->qrcode_data_string, $i, 1)];
						$this->data_bits[$data_counter]  = 6;
					}
					else
					{
						$this->data_value[$this->data_counter] = $this->data_value[$this->data_counter] * 45 + $alphanumeric_character_hash[substr($this->qrcode_data_string, $i, 1)];
						$this->data_bits[$this->data_counter]  = 11;
						$this->data_counter++;
					}
				}
			}
		}
		else
		{
			$this->codeword_num_plus = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 
											 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 
											 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4);

			$this->data_value[$this->data_counter] = 1;
			$this->data_counter++;
			$this->data_value[$this->data_counter] = $this->data_length;
			$this->data_bits[$this->data_counter]  = 10;
			$this->codeword_num_counter_value      = $this->data_counter;
			$this->data_counter++;
	
			for($i = 0; $i < $this->data_length; $i++)
			{
				if ( ($i % 3) == 0 )
				{
					$this->data_value[$this->data_counter] = substr($this->qrcode_data_string, $i, 1);
					$this->data_bits[$this->data_counter]  = 4;
				}
				else
				{
					$this->data_value[$this->data_counter] = $this->data_value[$this->data_counter] * 10 + substr($this->qrcode_data_string, $i, 1);
					
					if ( ($i % 3) == 1 )
					{
						$this->data_bits[$this->data_counter] = 7;
					}
					else
					{
						$this->data_bits[$this->data_counter] = 10;
						$this->data_counter++;
					}
				}
			}
		}
		
		if ( @$this->data_bits[$this->data_counter] > 0 ) 
		{
			$this->data_counter++;
		}
		
		$this->total_data_bits = 0;
		
		for($i = 0; $i < $this->data_counter; $i++)
		{
			$this->total_data_bits += $this->data_bits[$i];
		}
		
		return $this;
	}
	
	private function determineEccMode()
	{
		$this->error_correct_mode = @$ecc_character_hash[$this->qrcode_error_correct]; 
		
		if ( !$this->error_correct_mode )
		{
			$this->error_correct_mode = 0;
		}
		
		return $this;
	}
	
	private function determineQrCodeVersion()
	{
		if ( !is_numeric($this->qrcode_version) )
		{
			$this->qrcode_version = 0;
		}
		
		if ( !$this->qrcode_version )
		{
			$i = 1 + 40 * $this->error_correct_mode;
			$j = $i + 39;
			$this->qrcode_version = 1;
			
			while ( $i <= $j )
			{
				if ( $this->max_data_bits_array[$i] >= $this->total_data_bits + $this->codeword_num_plus[$this->qrcode_version] )
				{
					$this->max_data_bits = $this->max_data_bits_array[$i];
					break;
				}
				
				$i++;
				$this->qrcode_version++;
			}
			
		}
		else 
		{
			 $this->max_data_bits = $this->max_data_bits_array[$this->qrcode_version + 40 * $this->error_correct_mode];
		}

		if ( $this->qrcode_version > $this->version_ul )
		{
			trigger_error("Swetake QR Code: too large version.",  E_USER_ERROR);
		}

		$this->total_data_bits                              += $this->codeword_num_plus[$this->qrcode_version];
		$this->data_bits[$this->codeword_num_counter_value] += $this->codeword_num_plus[$this->qrcode_version];

		$this->max_codewords     = $this->max_codewords_array[$this->qrcode_version];
		$this->max_modules_1side = 17 + ($this->qrcode_version <<2);

		$this->byte_num = $this->matrix_remain_bit[$this->qrcode_version] + ($this->max_codewords << 3);
		$filename       = $this->data_path . "/qrv" . $this->qrcode_version . "_" . $this->error_correct_mode . ".dat";
		
		$fp1   = fopen ($filename, "rb");
		
		$matx  = fread($fp1, $this->byte_num);
		$maty  = fread($fp1, $this->byte_num);
		$masks = fread($fp1, $this->byte_num);
		
		$fi_x  = fread($fp1, 15);
		$fi_y  = fread($fp1, 15);
		
		$this->rs_ecc_codewords = ord(fread($fp1, 1));
		$rso   = fread($fp1, 128);
		fclose($fp1);
		
		$this->matrix_x_array = unpack("C*", $matx);
		$this->matrix_y_array = unpack("C*", $maty);
		$this->mask_array = unpack("C*", $masks);
		
		$this->rs_block_order  =unpack("C*", $rso);
		
		$this->format_information_x2 = unpack("C*", $fi_x);
		$this->format_information_y2 = unpack("C*", $fi_y);
		
		$this->max_data_codewords = ($this->max_data_bits >>3);
		
		$filename = $this->data_path . "/rsc" . $this->rs_ecc_codewords . ".dat";
		$fp0 = fopen ($filename, "rb");
		
		for($i = 0; $i < 256; $i++)
		{
			$this->rs_cal_table_array[$i] = fread($fp0, $this->rs_ecc_codewords);
		}
		
		fclose ($fp0);
		
		$filename         = $this->data_path . "/qrvfr" . $this->qrcode_version . ".dat";
		$fp0              = fopen ($filename, "rb");
		$this->frame_data = fread ($fp0, filesize ($filename));
		
		fclose ($fp0);
		
		return $this;
	}
	
	private function determineTerminator()
	{
		if ( $this->total_data_bits <= $this->max_data_bits - 4 )
		{
			$this->data_value[$this->data_counter] = 0;
			$this->data_bits[$this->data_counter]  = 4;
		}
		else
		{
			if ( $this->total_data_bits < $this->max_data_bits )
			{
				$this->data_value[$this->data_counter] = 0;
				$this->data_bits[$this->data_counter]  = $this->max_data_bits - $this->total_data_bits;
			}
			else
			{
				if ( $this->total_data_bits > $this->max_data_bits )
				{
					trigger_error("Swetake QR Code: Overflow error.", E_USER_ERROR);
					exit;
				}
			}
		}
		
		return $this;
	}
	
	/* method */
	
	private function divideDataEightBit()
	{
		$this->codewords_counter = 0;
		$this->codewords[0]      = 0;
		$remaining_bits    = 8;
		
		for($i = 0; $i <= $this->data_counter; $i++)
		{
			$buffer      = @$this->data_value[$i];
			$buffer_bits = @$this->data_bits[$i];
		
			$flag = true;
			
			while( $flag )
			{
				if ( $remaining_bits > $buffer_bits )
				{  
					$this->codewords[$this->codewords_counter] = ((@$this->codewords[$this->codewords_counter]<<$buffer_bits) | $buffer);
					$remaining_bits -= $buffer_bits;
					
					$flag = false;
				}
				else
				{
					$buffer_bits -= $remaining_bits;
					$this->codewords[$this->codewords_counter] = (($this->codewords[$this->codewords_counter] << $remaining_bits) | ($buffer >> $buffer_bits));
		
					if ( $buffer_bits == 0 )
					{
						$flag = false;
					}
					else
					{
						$buffer = ($buffer & ((1 << $buffer_bits)-1) );
						$flag   = true;   
					}
		
					$this->codewords_counter++;
					
					if ( $this->codewords_counter < $this->max_data_codewords - 1 )
					{
						$this->codewords[$this->codewords_counter] = 0;
					}
					
					$remaining_bits = 8;
				}
			}
		}
		
		if ( $remaining_bits != 8 )
		{
			$this->codewords[$this->codewords_counter] = $this->codewords[$this->codewords_counter] << $remaining_bits;
		}
		else
		{
			$this->codewords_counter--;
		}
		
		return $this;
	}
	
	private function paddingCharacter()
	{
		if ( $this->codewords_counter < $this->max_data_codewords - 1 )
		{
			$flag = 1;
			
			while ( $this->codewords_counter < $this->max_data_codewords - 1 )
			{
				$this->codewords_counter++;
				
				if ( $flag == true)
				{
					$this->codewords[$this->codewords_counter] = 236;
				}
				else
				{
					$this->codewords[$this->codewords_counter] = 17;
				}
				
				$flag = $flag * (-1);
			}
		}
		
		return $this;
	}
	
	private function errorCorrection()
	{
		$j               = 0;
		$rs_block_number = 0;
		$rs_temp[0]      = "";
		
		for($i = 0; $i < $this->max_data_codewords; $i++)
		{
			$rs_temp[$rs_block_number] .= chr($this->codewords[$i]);
			$j++;
		
			if ( $j >= $this->rs_block_order[$rs_block_number + 1] - $this->rs_ecc_codewords )
			{
				$j = 0;
				$rs_block_number++;
				$rs_temp[$rs_block_number] = "";
			}
		}
		
		$rs_block_number = 0;
		$rs_block_order_num = count($this->rs_block_order);
		
		while ( $rs_block_number < $rs_block_order_num )
		{
			$rs_codewords      = $this->rs_block_order[$rs_block_number + 1];
			$rs_data_codewords = $rs_codewords - $this->rs_ecc_codewords;
		
			$rstemp       = $rs_temp[$rs_block_number] . str_repeat(chr(0), $this->rs_ecc_codewords);
			$padding_data = str_repeat(chr(0), $rs_data_codewords);
		
			$j = $rs_data_codewords;
			
			while( $j > 0 )
			{
				$first = ord(substr($rstemp, 0, 1));
		
				if ( $first )
				{
					$left_chr = substr($rstemp, 1);
					$cal      = $this->rs_cal_table_array[$first] . $padding_data;
					$rstemp   = $left_chr ^ $cal;
				}
				else
				{
					$rstemp = substr($rstemp, 1);
				}
		
				$j--;
			}
		
			$this->codewords = array_merge($this->codewords, unpack("C*", $rstemp));
		
			$rs_block_number++;
		}
		
		return $this;
	}
	
	private function flashMatrix()
	{
		for($i = 0; $i < $this->max_modules_1side; $i++)
		{
			for($j = 0; $j < $this->max_modules_1side; $j++)
			{
				$this->matrix_content[$j][$i] = 0;
			}
		}
		
		return $this;
	}
	
	private function attachData()
	{
		for($i = 0; $i < $this->max_codewords; $i++)
		{
			$codeword_i = $this->codewords[$i];
			$j=8;
			
			while ( $j >= 1 )
			{
				$codeword_bits_number = ($i << 3) +  $j;
				$this->matrix_content[ $this->matrix_x_array[$codeword_bits_number] ][ $this->matrix_y_array[$codeword_bits_number] ]=((255 * ($codeword_i & 1)) ^ $this->mask_array[$codeword_bits_number] ); 
				$codeword_i = $codeword_i >> 1;
				$j--;
			}
		}
		
		$matrix_remain = $this->matrix_remain_bit[$this->qrcode_version];
		
		while ($matrix_remain)
		{
			$remain_bit_temp = $matrix_remain + ( $this->max_codewords <<3);
			$matrix_content[ $this->matrix_x_array[$remain_bit_temp] ][ $this->matrix_y_array[$remain_bit_temp] ]  =  ( 255 ^ $this->mask_array[$remain_bit_temp] );
			$matrix_remain--;
		}
		
		return $this;
	}
	
	private function maskSelect()
	{
		$min_demerit_score = 0;
		$hor_master        = "";
		$ver_master        = "";
		
		for($i = 0; $i < $this->max_modules_1side; $i++)
		{
			for($j = 0; $j < $this->max_modules_1side; $j++)
			{
				$hor_master = $hor_master . chr($this->matrix_content[$j][$i]);
				$ver_master = $ver_master . chr($this->matrix_content[$i][$j]);
			}
		}
		
		$all_matrix = $this->max_modules_1side * $this->max_modules_1side; 
		
		for($i = 0; $i < 8; $i++)
		{
			$demerit_n1 = 0;
			$ptn_temp   = array();
			$bit        = 1<< $i;
			$bit_r      = (~$bit)&255;
			$bit_mask   = str_repeat(chr($bit), $all_matrix);
			$hor        = $hor_master & $bit_mask;
			$ver        = $ver_master & $bit_mask;
		
			$ver_shift1   = $ver.str_repeat(chr(170), $this->max_modules_1side);
			$ver_shift2   = str_repeat(chr(170), $this->max_modules_1side) . $ver;
			$ver_shift1_0 = $ver.str_repeat(chr(0), $this->max_modules_1side);
			$ver_shift2_0 = str_repeat(chr(0), $this->max_modules_1side) . $ver;
			$ver_or       = chunk_split(~($ver_shift1 | $ver_shift2), $this->max_modules_1side, chr(170));
			$ver_and      = chunk_split(~($ver_shift1_0 & $ver_shift2_0), $this->max_modules_1side, chr(170));
		
			$hor = chunk_split(~$hor ,$this->max_modules_1side, chr(170));
			$ver = chunk_split(~$ver, $this->max_modules_1side, chr(170));
			$hor = $hor . chr(170) . $ver;
		
			$n1_search  ="/" . str_repeat(chr(255), 5) . "+|" . str_repeat(chr($bit_r), 5) . "+/";
			$n3_search  = chr($bit_r) . chr(255) .chr($bit_r) . chr($bit_r) . chr($bit_r) . chr(255) . chr($bit_r);
		
			$demerit_n3 = substr_count($hor, $n3_search) * 40;
			$demerit_n4 = floor(abs(( (100 * (substr_count($ver, chr($bit_r)) / ($this->byte_num)) ) - 50) / 5)) * 10;
		
			$n2_search1 = "/" . chr($bit_r) . chr($bit_r) . "+/";
			$n2_search2 = "/" . chr(255) . chr(255) . "+/";
			$demerit_n2 = 0;
			preg_match_all($n2_search1, $ver_and, $ptn_temp);
			
			foreach($ptn_temp[0] as $str_temp)
			{
				$demerit_n2 += (strlen($str_temp) - 1);
			}
			
			$ptn_temp = array();
			preg_match_all($n2_search2, $ver_or, $ptn_temp);
			
			foreach($ptn_temp[0] as $str_temp)
			{
				$demerit_n2 += (strlen($str_temp) - 1);
			}
		   
			$demerit_n2 *= 3;
			$ptn_temp = array();
			preg_match_all($n1_search, $hor, $ptn_temp);
		   
			foreach($ptn_temp[0] as $str_temp)
			{
				$demerit_n1 += (strlen($str_temp) - 2);
			}
		
			$demerit_score = $demerit_n1 + $demerit_n2 + $demerit_n3 + $demerit_n4;
		
			if ( $demerit_score <= $min_demerit_score || $i == 0 )
			{
				$this->mask_number = $i;
				$min_demerit_score = $demerit_score;
			}
		
		}
		
		$this->mask_content = 1 << $this->mask_number;
		return $this;
	}
	
	private function formatInformation()
	{
		$format_information_value = (($this->error_correct_mode << 3) | $this->mask_number);
		
		for ($i = 0; $i < 15; $i++)
		{
			$content = substr($this->format_information_array[$format_information_value],$i,1);
		
			$this->matrix_content[$this->format_information_x1[$i]][$this->format_information_y1[$i]]     = $content * 255;
			$this->matrix_content[$this->format_information_x2[$i+1]][$this->format_information_y2[$i+1]] = $content * 255;
		}
		
		$this->mib = $this->max_modules_1side + 8;
		$this->qrcode_image_size = $this->mib * $this->qrcode_module_size;
		
		if ( $this->qrcode_image_size > 1480 )
		{
			trigger_error("Swetake QR Code: Too large image size.", E_USER_ERROR);
		}
		
		/* old */
		/*
		
		$this->output_image = imagecreate($this->qrcode_image_size, $this->qrcode_image_size);
		$this->image_path   = $this->image_path . "/qrv" . $this->qrcode_version . ".png";
		$this->base_image   = imagecreatefrompng($this->image_path);
		
		$col[1] = imagecolorallocate($this->base_image, 0, 0, 0);
		$col[0] = imagecolorallocate($this->base_image, 255, 255, 255);
		
		$mxe   = 4 + $this->max_modules_1side;
		$outer = 0;
		
		for($i = 4; $i < $mxe; $i++)
		{
			$inner = 0;
			
			for($j = 4; $j < $mxe; $j++)
			{
				if ($this->matrix_content[$outer][$inner] & $this->mask_content)
				{
					imagesetpixel($this->base_image, $i, $j, $col[1]); 
				}
				
				$inner++;
			}
			
			$outer++;
		}
		
		imagecopyresized($this->output_image, 
						 $this->base_image, 
						 0, 
						 0, 
						 0, 
						 0, 
						 $this->qrcode_image_size, 
						 $this->qrcode_image_size, 
						 $this->mib, 
						 $this->mib);
		
		*/
		/* old */
		
		return $this;
	}
	
	private function fillDataArray()
	{
		$buffer = "";
		$mxe    = $this->max_modules_1side;
		
		for($i = 0; $i < $mxe; $i++)
		{
    		for($j = 0; $j < $mxe; $j++)
			{
				if ( $this->matrix_content[$j][$i] & $this->mask_content )
				{
					$buffer .= "1";
				}
				else
				{
					$buffer .= "0";
				}
    		}
			
			$buffer .= "\n";
   		}
		
		$this->image_data = $buffer | $this->frame_data;
		return $this;
	}
	
	/* image */
	
	private function drawImage()
	{
		$quiet_zone = 4;
		
		$this->image_data = explode("\n", $this->image_data);
        $c                = count($this->image_data) - 1;
        $image_size       = $c;
        $output_size      = ($c + ($quiet_zone) * 2) * $this->qrcode_module_size;

        $img = imagecreate($image_size, $image_size);
        $white = imagecolorallocate ($img, 255, 255, 255);
        $black = imagecolorallocate ($img, 0, 0, 0);

        $im = imagecreate($output_size, $output_size);

        $white2 = imagecolorallocate ($im, 255, 255, 255);
        imagefill($im, 0, 0, $white2);

        $y=0;
        foreach($this->image_data as $row)
		{
			for($x = 0; $x < $image_size; $x++)
			{
				if ( substr($row, $x, 1) == "1" )
				{
					imagesetpixel($img, $x, $y, $black);
				}
			}
			
			$y++;
        }
		
        $quiet_zone_offset = ($quiet_zone) * ($this->qrcode_module_size);
        $image_width       = $image_size * ($this->qrcode_module_size);

        imagecopyresized($im,
						 $img,
						 $quiet_zone_offset,
						 $quiet_zone_offset,
						 0,
						 0,
						 $image_width,
						 $image_width,
						 $image_size,
						 $image_size);

        $this->output_image = $im;
		return $this;
	}
	
	private function outputImage()
	{
		header("Content-type: image/" . $this->qrcode_image_type);
		
		if ( $this->qrcode_image_type == "jpeg" )
		{
			imagejpeg($this->output_image);
		}
		else
		{
			imagepng($this->output_image);
		}
		
		return $this;
	}
	
	/* cleanup */
	
	private function cleanup()
	{
		/* images */
		
		@imagedestroy($this->output_image);
		@imagedestroy($this->base_image);
	
		/* internal */
	
		unset($this->data_length);
		unset($this->data_counter);
		unset($this->data_value);
		unset($this->data_bits);
		unset($this->total_data_bits);
		unset($this->max_data_bits);
		unset($this->max_data_codewords);
		unset($this->codewords);
		unset($this->codewords_counter);
		unset($this->max_codewords);
		unset($this->codeword_num_plus);
		unset($this->codeword_num_counter_value);
		unset($this->error_correct_mode);
		unset($this->rs_block_order);
		unset($this->rs_ecc_codewords);
		unset($this->rs_cal_table_array);
		unset($this->max_modules_1side);
		unset($this->matrix_content);
		unset($this->matrix_x_array);
		unset($this->matrix_y_array);
		unset($this->mask_array);
		unset($this->mask_number);
		unset($this->byte_num);
		unset($this->format_information_x2);
		unset($this->format_information_y2);
		unset($this->frame_data);
	
		/* data arrays */
	
		unset($this->alphanumeric_character_hash);
		unset($this->ecc_character_hash);
		unset($this->max_data_bits_array);
		unset($this->max_codewords_array);
		unset($this->matrix_remain_bit);
		unset($this->format_information_x1);
		unset($this->format_information_y1);
		unset($this->format_information_array);
	
		/* image */
		
		unset($this->image_data);
		unset($this->output_image);
		unset($this->base_image);
		unset($this->mib);
		unset($this->qrcode_image_size);	
		
		return $this;
	}
}
