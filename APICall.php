<?php

	/*
		Name:		Simple API Caller 
		Desc:		API Caller Utility allows to make call to any api
		Version:	V1.0
		Date:		3/11/2017
		Author:		Prince Adeyemi
		Contact:	prince@vegasnewspaper.com
		Facebook:	fb.com/YourVegasPrince
		
		Usage:
		Assuming you want to call Amazon;
		create index.php or include this class where you wanted to use it;
		
		$prince = new PrinceAPICaller();  
		$result = $prince->_sendRequest('http://amazon.com/whatever/path/api/or/webpage');
		
		print_r( $result );
		
		Or use this to pass data to another resources.
		
			$data = array( 'username' => 'MyUsername', 'password' => 'Mypassword' );
		
			$prince->_sendRequest('http://amazon.com/whatever/path/api/or/webpage', $data );
			
		To use get method, just pass 'GET' into it.
			$prince = new PrinceAPICaller('GET');
			$result = $prince->_sendRequest('http//google.com');
			
			print_r( $result );
		
		
	*/

	
	if( !class_exists( 'PrinceAPICaller' ) )
	{
		class PrinceAPICaller
		{
			private $_ch;
			private $_error;
			private $_result;
			private $_cookieFile;
			private $_method;
			
			private $_ENC_METHOD 	= "AES-256-CBC";
			private $_ENC_KEY 		= "MySecretKey12345";
			private $_ENC_IV		= "mySecretemySecre";
			
			//Begins
			
			public function __Construct( $reqType='post' )
			{
				$this->_error = array( 
					'code' => '0', 
					'errorMessage' => '');
					
				$this->_ch = null;
				$this->_result = '';	
				$this->_cookieFile = 'supCookies.txt';
				
				$this->_method  = ( isset( $reqType ) && !empty( $reqType ) ) ? strtolower( $reqType ) : 'post' ;
			}
			
			public function _sendRequest( $endpoint, $data=null )
			{
				// safe check
				$endpoint = ( isset( $endpoint ) && !empty( $endpoint ) ) ? $endpoint : '';
				$postData = ( isset( $data ) && !empty( $data ) ) ? http_build_query( $data ) : null;
				
				
				if( empty( $endpoint ) )
				{
					$this->_error = array(
						'code' => 1 ,
						'errorMessage' => 'Uh oh, you must supply an endpoint to connect to!');
						
					return $this->_error;
				}
				
				// check if curl is installed
				if( !function_exists( 'curl_init' ) )
				{
					$this->_error = array(
						'code' => 2,
						'errorMessage' => 'Curl not installed, please install curl');
					
					return $this->_error;
				}
				
				$this->_ch = curl_init();
				
				if( $this->_method == 'post' )
				{
					curl_setopt( $this->_ch, CURLOPT_URL, $endpoint );
					curl_setopt( $this->_ch, CURLOPT_COOKIEJAR, $this->_cookieFile);
					curl_setopt( $this->_ch, CURLOPT_FOLLOWLOCATION, true ) ;
					curl_setopt( $this->_ch, CURLOPT_RETURNTRANSFER, true );
					curl_setopt( $this->_ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)" );
					curl_setopt( $this->_ch, CURLOPT_POST, true );
					curl_setopt( $this->_ch, CURLOPT_POSTFIELDS, $postData );
					curl_setopt( $this->_ch, CURLOPT_SSL_VERIFYHOST, FALSE );
					curl_setopt( $this->_ch, CURLOPT_SSL_VERIFYPEER, FALSE );
						
					$this->_result	= curl_exec( $this->_ch );
					
					if( curl_error( $this->_ch ) )
					{
						return curl_error( $this->_ch );
					}
					curl_close( $this->_ch );
					
						return $this->_result;
					
				} else
				
				if( $this->_method == 'get' )
				{
					curl_setopt( $this->_ch, CURLOPT_URL, $endpoint );
					curl_setopt( $this->_ch, CURLOPT_COOKIEJAR, $this->_cookieFile);
					curl_setopt( $this->_ch, CURLOPT_FOLLOWLOCATION, true );
					curl_setopt( $this->_ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt( $this->_ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");
					curl_setopt( $this->_ch, CURLOPT_SSL_VERIFYHOST, FALSE );
					curl_setopt( $this->_ch, CURLOPT_SSL_VERIFYPEER, FALSE );
						
					$this->_result	= curl_exec( $this->_ch );
					
					if( curl_error( $this->_ch ) )
					{
						return curl_error( $this->_ch );
					}
					curl_close( $this->_ch);
					
					return $this->_result ;
				}
				else {
					$this->_error = array(
						'code' => 3,
						'errorMessage' => 'Invalid request, POST | GET allowed ');
					
					return $this->_error;
				}
				
			}// end of _sendRequest
			
			public function EncryptIt( $DataToEncrypt, $Method = null, $SecretKey=null, $iv = "mySecretemySecre" )
			{
				$Method = ( $Method !== null )  ? $Method : $this->_ENC_METHOD;
				$SecretKey = ( $SecretKey !== null )  ? $SecretKey : $this->_ENC_KEY;
				$iv = ( $iv !== null )  ? $iv : $this->_ENC_IV;
				
				$Encrypted = openssl_encrypt( $DataToEncrypt, $Method, $SecretKey, 0, $iv ); 
				if( $Encrypted )
				{
					return $Encrypted;
				}
			}


			public function DecryptIt( $EncryptedData, $Method = null, $SecretKey=null, $iv = null )
			{
				$Method = ( $Method !== null )  ? $Method : $this->_ENC_METHOD;
				$SecretKey = ( $SecretKey !== null )  ? $SecretKey : $this->_ENC_KEY;
				$iv = ( $iv !== null )  ? $iv : $this->_ENC_IV;
				
				$Decrypted = openssl_decrypt( $EncryptedData, $Method, $SecretKey, 0, $iv );
				
				if( $Decrypted )
				{
					return $Decrypted;
				}
			}		
			
		} // end of class
	} //end of class checking
?>