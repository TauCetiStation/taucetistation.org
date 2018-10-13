<?php

//based on /tg/station and maybe goonstation export, I'm not sure
class Byond {

	private $cacheFolder = __DIR__ . '/../cache/byond/';
	private $timeout = 30;//seconds

	public function getServerStatus($address, $port) {
		return $this->getData($address, $port, '?status');
	}

	public function getData($address, $port, $query) {
		$objData = Array();

		if(!is_dir($this->cacheFolder)) {
			mkdir($this->cacheFolder, 0777, true);

			if(!is_dir($this->cacheFolder)) {
				throw new RuntimeException(sprintf('Unable to create the cache directory (%s).', $dir));
			}
		}

		$cacheFile = $this->cacheFolder . hash('CRC32', $address . $port . $query);//todo: memcache someday

		if(file_exists($cacheFile) && time() - filemtime($cacheFile) <= $this->timeout) {
			$objData = unserialize(file_get_contents($cacheFile));
			$objData['cached'] = 1;
			return $objData;
		}

		$rawData = $this->export($address, $port, $query);

		if($rawData && $rawData != "") {
			$rawData = str_replace("\x00", "", $rawData);

			// Split the information into easily-accessible arrays
			$array_data = explode("&", $rawData);

			for($i = 0; $i < count($array_data); $i++) {
				//Split the row by the = sign into the identifier at index 0 and the value at index 1 (if the value exists)
				$row = explode("=", $array_data[$i]);
				if(isset($row[1])){
					//All should go here... but just in case.
					$objData[$row[0]] = urldecode($row[1]);
					if(is_numeric($objData[$row[0]])) {
						$objData[$row[0]] = (int)$objData[$row[0]];
					}
				}else{
					$objData[$row[0]] = null;
				}
			}

			file_put_contents($cacheFile, serialize($objData));

			return $objData;

		}

		//can't get new data, let's check old
		if(file_exists($cacheFile)) {
			$objData = unserialize(file_get_contents($cacheFile));
			$objData['cached'] = 1;
		} else {
			//can't get data...
			$objData['error'] = 1;
		}

		//create/update cache file for timeout
		file_put_contents($cacheFile, serialize($objData));

		return $objData;
	}
	
	private function export($address, $port, $query) {
		// All queries must begin with a question mark (ie "?players")
		if($query{0} != '?') {
			$query = ('?' . $query);
		}

		$query = "\x00\x83" . pack( 'n', strlen($query) + 6 ) . "\x00\x00\x00\x00\x00" . $query . "\x00";

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

		if(!$socket) {
			//Unable to create socket
			return Null;
		}

		if(!$this->connect_with_timeout($socket, $address, $port)) {
			//Unable to connect to socket
			return Null;
		}

		// Set two second timeout on socket read and write
		socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 2, "usec" => 0 ));
		socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => 2, "usec" => 0 ));

		$bytestosend = strlen($query);
		$bytessent = 0;
		while ($bytessent < $bytestosend) {
			$result = socket_write($socket, substr($query, $bytessent), $bytestosend - $bytessent);

			if ($result === FALSE) {
				//Connection error
				return Null;
			}
			$bytessent += $result;
		};

		$result = socket_read($socket, 10000, PHP_BINARY_READ);
		socket_close($socket);

		if($result === "") {
			//Null data
			return Null;
		}

		if($result{0} == "\x00" || $result{1} == "\x83") {    // make sure it's the right packet format
			// Actually begin reading the output:
			$sizebytes = unpack('n', $result{2} . $result{3}); // array size of the type identifier and content
			$size = $sizebytes[1] - 1;                         // size of string/floating-point, less the identifier byte

			if($result{4} == "\x2a") {                         // 4-byte big-endian floating-point
				                                                // 4 possible bytes: add them up together, unpack them
				                                                // as a floating-point
				$unpackint = unpack('f', $result{5} . $result{6} . $result{7} . $result{8}); 
				return $unpackint[1];
			}
			else if($result{4} == "\x06") {                    // ASCII string
				$unpackstr = "";                                // Initialize result string
				$index = 5;                                     // string index

				while($size > 0) {                          	   // loop through the entire ASCII string
					$size--;
					$unpackstr .= $result{$index};               // add the string position to return string
					$index++;
				}
				return $unpackstr;
			}
		}

		return Null;
	}

	private function connect_with_timeout($soc, $host, $port, $timeout = 3) {
		$con_per_sec = 100;
		socket_set_nonblock($soc);
		for($i=0; $i<($timeout * $con_per_sec); $i++) { 
			@socket_connect($soc, $host, $port);
			if(socket_last_error($soc) == SOCKET_EISCONN) { 
				break;
			};
			usleep(1000000 / $con_per_sec);
		}
		socket_set_block($soc);
		return socket_last_error($soc) == SOCKET_EISCONN;
	}
}
