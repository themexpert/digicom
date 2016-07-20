<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

class DigiComSiteHelperDownloadFile {
	private $filepath;
	private $file_ext;

	function __construct($filepath, $basefileLink){
		$this->filepath = $filepath;
		$this->basefilepath = $basefileLink;

	}
	/*
	* download method
	* $type = int; 1= unique download link, no direct link to file
	*/
	function download($type = 0, $directLink = 0, $externalLink = 0)
	{
		$app					= JFactory::getApplication();
		$params 			= $app->getParams();
		$absOrRelFile	= $this->basefilepath;

		$parsed = parse_url($this->filepath);
		if (!empty($parsed['scheme'])) {
			$externalLink = 1;
		}

		// Type = 1 - Token - unique download link - cannot be direct
		if ($type == 1) {
			$directLink = 0;
		}

		if($absOrRelFile) {

			// Get extensions
			$this->file_ext = JFile::getExt(strtolower($absOrRelFile));

			// next in future to control download filetype
			$aft = ''; //allowed_file_types_download
			$dft = ''; //disallowed_file_types_download

			// get the match case
			$allowedMimeType = true; //getMimeType($this->file_ext, $aft);
			$disallowedMimeType = false; //getMimeType($this->file_ext, $dft);

			// NO MIME FOUND
			$errorAllowed 		= false;// !!! IF YES - Disallow Downloading
			$errorDisallowed 	= false;// !!! IF YES - Allow Downloading

			if($allowedMimeType){
				$errorAllowed 		= false;
			}
			if(!$disallowedMimeType){
				$errorDisallowed	= true;
			}

			if(!$errorAllowed && $errorDisallowed) 
			{
				if ($directLink == 1 or $externalLink)
				{
					// Direct Link on the same server
					if (empty($parsed['scheme'])) {
						$downloadpath = JURI::root(true) . $this->filepath;
					}else{
						$downloadpath = $this->filepath;
					}

					if($directLink != 1)
					{
						$name = basename($downloadpath);
						header("Content-Description: File Transfer");
						header("Content-Type: application/octet-stream");
						header("Content-Disposition: attachment; filename=" .  $name);
						@readfile($downloadpath);						
					}
					else
					{
						$app->redirect ($downloadpath);
					}

					exit;
				}
				else 
				{

					$fileWithoutPath	= basename($absOrRelFile);
					$fileSize 			= filesize($absOrRelFile);
					$mimeType			= '';

					// set the mime type based on extension, add yours if needed.
					$mimeType_default = "application/octet-stream";
					$content_types = array(
							"exe" => "application/octet-stream",
							"zip" => "application/zip",
							"mp3" => "audio/mpeg",
							"mpg" => "video/mpeg",
							"avi" => "video/x-msvideo",
					);
					$mimeType = isset($content_types[$this->file_ext]) ? $content_types[$this->file_ext] : $mimeType_default;

					if ($fileSize == 0 ) {
						die(JText::_('COM_DIGICOM_FILE_SIZE_EMPTY'));
						exit;
					}

					// Clean the output buffer
					ob_end_clean();
					// test for protocol and set the appropriate headers
				    jimport( 'joomla.environment.uri' );
				    $_tmp_uri 					= JURI::getInstance( JURI::current() );
				    $_tmp_protocol 			= $_tmp_uri->getScheme();
					if ($_tmp_protocol 	== "https") {
						// SSL Support
						header('Cache-Control: private, max-age=0, must-revalidate, no-store');
			    	} else {
						header("Cache-Control: public, must-revalidate");
						header('Cache-Control: pre-check=0, post-check=0, max-age=0');
						header("Pragma: no-cache");
						header("Expires: 0");
					} /* end if protocol https */

					header("Content-Description: File Transfer");
					header("Expires: Sat, 30 Dec 1990 07:07:07 GMT");
					header("Accept-Ranges: bytes");

					// Modified by Rene
					// HTTP Range - see RFC2616 for more informations (http://www.ietf.org/rfc/rfc2616.txt)
					$httpRange   = 0;
					$newFileSize = $fileSize - 1;
					// Default values! Will be overridden if a valid range header field was detected!
					$resultLenght = (string)$fileSize;
					$resultRange  = "0-".$newFileSize;
					// We support requests for a single range only.
					// So we check if we have a range field. If yes ensure that it is a valid one.
					// If it is not valid we ignore it and sending the whole file.
					if(isset($_SERVER['HTTP_RANGE']) && preg_match('%^bytes=\d*\-\d*$%', $_SERVER['HTTP_RANGE'])) {
						// Let's take the right side
						list($a, $httpRange) = explode('=', $_SERVER['HTTP_RANGE']);
						// and get the two values (as strings!)
						$httpRange = explode('-', $httpRange);
						// Check if we have values! If not we have nothing to do!
						if(!empty($httpRange[0]) || !empty($httpRange[1])) {
							// We need the new content length ...
							$resultLenght	= $fileSize - $httpRange[0] - $httpRange[1];
							// ... and we can add the 206 Status.
							header("HTTP/1.1 206 Partial Content");
							// Now we need the content-range, so we have to build it depending on the given range!
							// ex.: -500 -> the last 500 bytes
							if(empty($httpRange[0]))
								$resultRange = $resultLenght.'-'.$newFileSize;
							// ex.: 500- -> from 500 bytes to filesize
							elseif(empty($httpRange[1]))
								$resultRange = $httpRange[0].'-'.$newFileSize;
							// ex.: 500-1000 -> from 500 to 1000 bytes
							else
								$resultRange = $httpRange[0] . '-' . $httpRange[1];
						}
					}
					
					header("Content-Length: ". $resultLenght);
					header("Content-Range: bytes " . $resultRange . '/' . $fileSize);
					header("Content-Type: " . (string)$mimeType);
					header('Content-Disposition: attachment; filename="'.$fileWithoutPath.'"');
					header("Content-Transfer-Encoding: binary\n");

					// TEST TEMP SOLUTION - makes problems on somve server, @ added to prevent from warning
					@ob_end_clean();

					// Try to deliver in chunks
					@set_time_limit(0);
					$fp = @fopen($absOrRelFile, 'rb');
					if ($fp !== false) {
						while (!feof($fp)) {
							echo fread($fp, 8192);
						}
						fclose($fp);
					} else {
						@readfile($absOrRelFile);
					}
					flush();
					exit;
				}
			}

		}
		return false;

	}

}
