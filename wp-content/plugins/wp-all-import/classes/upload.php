<?php
if ( ! class_exists('PMXI_Upload')){

	class PMXI_Upload{

		protected $file;
		protected $errors;
		protected $root_element = '';
		protected $is_csv = false;

		protected $uploadsPath;

		function __construct( $file,  $errors, $targetDir = false ){

			$this->file = $file;
			$this->errors = $errors;

			$uploads = wp_upload_dir();

			if ( $uploads['error'] )
				$this->uploadsPath = false;			
			else
				$this->uploadsPath = ( ! $targetDir ) ? wp_all_import_secure_file($uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::UPLOADS_DIRECTORY) : $targetDir;
		}

		public function upload(){

			$uploads = wp_upload_dir();

			if (empty($this->file)) {
				$this->errors->add('form-validation', __('Please specify a file to import.<br/><br/>If you are uploading the file from your computer, please wait for it to finish uploading (progress bar at 100%), before trying to continue.', 'wp_all_import_plugin'));				
			} elseif (!is_file($this->file)) {
				$this->errors->add('form-validation', __('Uploaded file is empty', 'wp_all_import_plugin'));
			} elseif ( ! preg_match('%\W(xml|gzip|zip|csv|gz|json|txt|dat|psv|sql)$%i', trim(basename($this->file)))) {				
				$this->errors->add('form-validation', __('Uploaded file must be XML, CSV, ZIP, GZIP, GZ, JSON, SQL, TXT, DAT or PSV', 'wp_all_import_plugin'));
			} elseif (preg_match('%\W(zip)$%i', trim(basename($this->file)))) {
										
				include_once(PMXI_Plugin::ROOT_DIR.'/libraries/pclzip.lib.php');

				$archive = new PclZip($this->file);
			    if (($v_result_list = $archive->extract(PCLZIP_OPT_PATH, $this->uploadsPath, PCLZIP_OPT_REPLACE_NEWER)) == 0) {
			    	$this->errors->add('form-validation', __('WP All Import couldn\'t find a file to import inside your ZIP.<br/><br/>Either the .ZIP file is broken, or doesn\'t contain a file with an extension of  XML, CSV, PSV, DAT, or TXT. <br/>Please attempt to unzip your .ZIP file on your computer to ensure it is a valid .ZIP file which can actually be unzipped, and that it contains a file which WP All Import can import.', 'wp_all_import_plugin'));		
			   	}
				else {
					
					$filePath = '';

					if (!empty($v_result_list)){
						foreach ($v_result_list as $unzipped_file) {							
							if ($unzipped_file['status'] == 'ok' and preg_match('%\W(xml|csv|txt|dat|psv|json)$%i', trim($unzipped_file['stored_filename']))) { $filePath = $unzipped_file['filename']; break; }
						}
					}
			    	if ( $this->uploadsPath === false ){
						$this->errors->add('form-validation', __('WP All Import can\'t access your WordPress uploads folder.', 'wp_all_import_plugin'));
					}

					if(empty($filePath)){						
						$zip = zip_open(trim($this->file));
						if (is_resource($zip)) {														
							while ($zip_entry = zip_read($zip)) {
								$filePath = zip_entry_name($zip_entry);												
							    $fp = fopen($this->uploadsPath."/".$filePath, "w");
							    if (zip_entry_open($zip, $zip_entry, "r")) {
							      $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							      fwrite($fp,"$buf");
							      zip_entry_close($zip_entry);
							      fclose($fp);
							    }
							    break;
							}
							zip_close($zip);							

						} else {
					        $this->errors->add('form-validation', __('WP All Import couldn\'t find a file to import inside your ZIP.<br/><br/>Either the .ZIP file is broken, or doesn\'t contain a file with an extension of  XML, CSV, PSV, DAT, or TXT. <br/>Please attempt to unzip your .ZIP file on your computer to ensure it is a valid .ZIP file which can actually be unzipped, and that it contains a file which WP All Import can import.', 'wp_all_import_plugin'));		
					    }						
					}																

					// Detect if file is very large					
					$source = array(
						'name' => basename($this->file),
						'type' => 'upload',							
						'path' => $this->file,					
					); 

					if (preg_match('%\W(csv|txt|dat|psv)$%i', trim($filePath))){ // If CSV file found in archieve						

						if($this->uploadsPath === false){
							 $this->errors->add('form-validation', __('WP All Import can\'t access your WordPress uploads folder.', 'wp_all_import_plugin'));
						}																								
						
						include_once(PMXI_Plugin::ROOT_DIR.'/libraries/XmlImportCsvParse.php');
						$csv = new PMXI_CsvParser( array( 'filename' => $filePath, 'targetDir' => $this->uploadsPath ) ); // create chunks
						//wp_all_import_remove_source($filePath, false);
						$filePath = $csv->xml_path;
						$this->is_csv = $csv->is_csv;
						$this->root_element = 'node';		
						
					} elseif (preg_match('%\W(json)$%i', trim($filePath))){

						$json_str = file_get_contents($filePath);
						$is_json = wp_all_import_is_json($json_str);
						
						if( is_wp_error($is_json)){
							$this->errors->add('form-validation', $is_json->get_error_message(), 'wp_all_import_plugin');
						}
						else{					
							
							$xml_data = wp_all_import_json_to_xml( json_decode($json_str, true) );

							if ( empty($xml_data) ){
								$this->errors->add('form-validation', __('Can not import this file. JSON to XML convertation failed.', 'wp_all_import_plugin'));	
							}
							else{
								$jsontmpname = $this->uploadsPath  .'/'. wp_all_import_url_title(wp_unique_filename($this->uploadsPath, str_replace("json", "xml", basename($filePath))));																
								file_put_contents($jsontmpname, $xml_data);
								wp_all_import_remove_source($filePath, false);
								$filePath = $jsontmpname;
							}
						}

					} elseif (preg_match('%\W(sql)$%i', trim($filePath))){						

						include_once( PMXI_Plugin::ROOT_DIR . '/libraries/XmlImportSQLParse.php' );	

						$localSQLPath = $filePath;
						$sql = new PMXI_SQLParser( $localSQLPath, $this->uploadsPath );
						$filePath = $sql->parse();
						wp_all_import_remove_source($localSQLPath, false);
					}					
				}

			} elseif ( preg_match('%\W(csv|txt|dat|psv)$%i', trim($this->file))) { // If CSV file uploaded
					
				if ( $this->uploadsPath === false ){
					 $this->errors->add('form-validation', __('WP All Import can\'t access your WordPress uploads folder.', 'wp_all_import_plugin'));
				}									
    			$filePath = $this->file;
				$source = array(
					'name' => basename($this->file),
					'type' => 'upload',
					'path' => $filePath,
				);								
				include_once(PMXI_Plugin::ROOT_DIR.'/libraries/XmlImportCsvParse.php');	

				$csv = new PMXI_CsvParser( array( 'filename' => $this->file, 'targetDir' => $this->uploadsPath ) );	
				//@unlink($filePath);				
				$filePath = $csv->xml_path;
				$this->is_csv = $csv->is_csv;
				$this->root_element = 'node';				
						
			} elseif(preg_match('%\W(gz)$%i', trim($this->file))){ // If gz file uploaded

				$fileInfo = wp_all_import_get_gz($this->file, 0, $this->uploadsPath);

				if ( ! is_wp_error($fileInfo) ){

					$filePath = $fileInfo['localPath'];				
					
					// Detect if file is very large				
					$source = array(
						'name' => basename($this->file),
						'type' => 'upload',
						'path' => $this->file,					
					);

					// detect CSV or XML 
					if ( $fileInfo['type'] == 'csv') { // it is CSV file									
						
						include_once(PMXI_Plugin::ROOT_DIR.'/libraries/XmlImportCsvParse.php');					
						$csv = new PMXI_CsvParser( array( 'filename' => $filePath, 'targeDir' => $this->uploadsPath ) ); // create chunks
						//@unlink($filePath);
						$filePath = $csv->xml_path;
						$this->is_csv = $csv->is_csv;
						$this->root_element = 'node';						
					
					}

				}
				else $this->errors->add('form-validation', $fileInfo->get_error_message());

			} elseif (preg_match('%\W(json)$%i', trim($this->file))){

				// Detect if file is very large				
				$source = array(
					'name' => basename($this->file),
					'type' => 'upload',
					'path' => $this->file,					
				);

				$json_str = file_get_contents($this->file);
				$is_json = wp_all_import_is_json($json_str);
				
				if( is_wp_error($is_json)){
					$this->errors->add('form-validation', $is_json->get_error_message(), 'wp_all_import_plugin');
				}
				else{					
					
					$xml_data = wp_all_import_json_to_xml( json_decode($json_str, true) );

					if ( empty($xml_data) ){
						$this->errors->add('form-validation', __('Can not import this file. JSON to XML convertation failed.', 'wp_all_import_plugin'));	
					}
					else{
						$jsontmpname = $this->uploadsPath  .'/'. wp_all_import_url_title(wp_unique_filename($this->uploadsPath, str_replace("json", "xml", basename($this->file))));
						//@unlink($this->file);
						file_put_contents($jsontmpname, $xml_data);
						$filePath = $jsontmpname;       
						
					}
				}

			} elseif (preg_match('%\W(sql)$%i', trim($this->file))){

				$source = array(
					'name' => basename($this->file),
					'type' => 'upload',
					'path' => $this->file,					
				);

				include_once( PMXI_Plugin::ROOT_DIR . '/libraries/XmlImportSQLParse.php' );	

				$sql = new PMXI_SQLParser( $this->file, $this->uploadsPath );
				$filePath = $sql->parse();		
				//@unlink($this->file);		

			} else { // If XML file uploaded				

				// Detect if file is very large
				$filePath = $this->file;
				$source = array(
					'name' => basename($this->file),
					'type' => 'upload',
					'path' => $filePath,
				);

			}

			if ( $this->errors->get_error_codes() ) return $this->errors;

			return array(
				'filePath' => $filePath,
				'source' => $source,
				'root_element' => $this->root_element,
				'is_csv' => $this->is_csv
			);
		}		
	}
}