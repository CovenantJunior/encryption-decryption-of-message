<?php
	function encryptFile($source, $key, $dest){
		define('FILE_ENCRYPTION_BLOCKS', 10000);
		$key = substr(sha1($key, true), 0, 16);
		$iv = openssl_random_pseudo_bytes(16);
		$error = false;
		if ($fpOut = fopen($dest, 'w')) {
			// Put the initialzation vector to the beginning of the file
			fwrite($fpOut, $iv);
			if ($fpIn = fopen($source, 'rb')) {
				while (!feof($fpIn)) {
					$plaintext = fread($fpIn, 16 * FILE_ENCRYPTION_BLOCKS);
					$ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA,
					$iv);
					// Use the first 16 bytes of the ciphertext as the next initialization vector
					$iv = substr($ciphertext, 0, 16);
					fwrite($fpOut, $ciphertext);
				}
				fclose($fpIn);
			}
			else {
				$error = true;
			}
			fclose($fpOut);
		}
		else {
			$error = true;
		}
		return $error ? false : $dest;
	}

	$source = 'me.txt';
	$key = $_COOKIE['handshake'];
	$dest = 'encrypted.enc';

	encryptFile($source, $key, $dest);

?>