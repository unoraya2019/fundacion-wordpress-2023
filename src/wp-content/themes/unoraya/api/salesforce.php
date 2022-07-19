<?php
    // Obtener el token
    function jsonEncode(array $input): string{
        if (PHP_VERSION_ID >= 50400) {
            $json = \json_encode($input, \JSON_UNESCAPED_SLASHES);
        } else {
            // PHP 5.3 only
            $json = \json_encode($input);
        }
        if ($errno = \json_last_error()) {
            handleJsonError($errno);
        } elseif ($json === 'null' && $input !== null) {
            throw new DomainException('Null result with non-null input');
        }
        if ($json === false) {
            throw new DomainException('Provided object could not be 
        encoded to valid JSON');
        }
        return $json;
    }
    
    function handleJsonError(int $errno): void{
        $messages = [
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters' //PHP >= 5.3.3
        ];
        throw new DomainException(
            isset($messages[$errno])
            ? $messages[$errno]
            : 'Unknown JSON error: ' . $errno
        );
    }

    function urlsafeB64Encode(string $input): string{
        return \str_replace('=', '', \strtr(\base64_encode($input), '+/', '-_'));
    }

    function get_data_api(){
        $uri = "https://test.salesforce.com/services/oauth2/token";
        
        $payload = [
            'iss' => '3MVG9AzPSkglhtpvDFwSx.C0wQmjYrFrxBgioBfCnjMmsi7pcJkjgw8yMa6eOq8Az52ikPi2.21yOSZHtDRdw',
            'sub' => 'vassadmin@fundacionbd.org.promusico',
            'aud' => 'https://test.salesforce.com',
            'exp' => time() + 3,
        ];
        
        $header = ['alg' => 'RS256'];
        $algo = "sha256WithRSAEncryption";
        
        $file = './server.key';

        if (file_exists($file)) {
            $private_key = file_get_contents($file, true);
        }

        $binary_signature = "";
        $segments = [];
        $segments[] = urlsafeB64Encode((string) jsonEncode($header));
        $segments[] = urlsafeB64Encode((string) jsonEncode($payload));
        $signing_input = \implode('.', $segments);
                
        openssl_sign($signing_input, $binary_signature, $private_key, $algo);
        $signature = $binary_signature;
        
        $segments[] = urlsafeB64Encode($signature);
        
        $jwt = implode('.', $segments);
                
        $response = wp_remote_post( $uri, array(
                'method'      => 'POST',
                'headers'     => array(
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ),
                'body'        => array(
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt
                )
            )
        );
         
    	if (is_wp_error($response)) {
    		error_log("Error: ". $response->get_error_message());
    		return false;
    	}
    	$body = wp_remote_retrieve_body($response);
    	$data = json_decode($body);
    	return $data->access_token;
    }
    
    // Envío de formulario
    function sendFormContactSalesforce($req, $accesToken) {
        $data = setPayloadSendForm($req);
        $url = "https://funbolivar--promusico.my.salesforce.com/services/data/v51.0/sobjects/Contact/";
        $response = wp_remote_post( $url, array(
            'body'    => json_encode($data),
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$accesToken,
            ),
        ));
        if (is_wp_error($response)) {
    		error_log("Error: ". $response->get_error_message());
    		return null;
    	}
    	$body = wp_remote_retrieve_body($response);
    	$res = json_decode($body);
    	if ($res[0] && $res[0]->duplicateResut && $res[0]->duplicateResut->matchResults[0]) {
    	    return validar_duplicado($req, $res[0]->duplicateResut->matchResults[0]->matchRecords[0]->record->Id, $accesToken);
    	}
    	return $res;
    }
    
    function validar_duplicado($req, $id, $accesToken) {
        $data = setPayloadSendForm($req);
        $url = "https://funbolivar--promusico.my.salesforce.com/services/data/v51.0/sobjects/Contact/".$id;
        $response = wp_remote_request($url, array(
            'method' => 'PATCH',
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$accesToken,
            ),
            'body' => json_encode($data)
        ));
        if (is_wp_error($response)) {
    		error_log("Error: ". $response->get_error_message());
    		return 'error';
    	}
    	$body = wp_remote_retrieve_body($response);
    	$res = json_decode($body);
    	return $res;
    }
    
    function setPayloadSendForm($req) {
        $data = array(
            'FirstName' => $req['nombre'],
			'LastName' => $req['apellidos'],
			'Tipo_Documento_Identidad__c' => $req['tipo_documento'],
            'Documento_de_Identidad__c' => $req['documento'],
            'Phone' => $req['tel'],
            'MobilePhone' => $req['tel'],
            'Email' => $req['email'],
            'fbd_ComoEnteroFBD__c' => $req['comoMeEntero'],
            'fbd_Comentarios_u_Observaciones__c' => $req['mensaje'],
            'fbd_Cargo__c' => $req['empresa'],
            'RecordTypeId' => '0124M000000ShpNQAS',
            'FBDX_PaisContacto__c' => $req['pais'],
            'FBDX_DepartamentoContacto__c' => $req['depto'],
            'FBDX_CiudadMunicipioContacto__c' => $req['ciudad'],
            'VOL_Acepto_Terminos_y_Condiciones__c' => true,
            'IP_Origen__c' => $_SERVER['REMOTE_ADDR'],
            'fbd_FechaAceptacionHabeasData__c' => date('Y-m-d'),
            'FBDX_AceptoPoliticaHabeasData__c' => true,
        );
        return $data;
    }

    function api_funda_salesforce($req) {
        $key  = $req->get_header('token');
    	$content = sendFormContactSalesforce($req, $key);
    	return $content;
    }
    
    function api_get_token() {
    	$token = get_data_api();
    	return $token;
    }
    
    // REGISTRO DE ENDPOINTS
    // Enviar formulario a Salesforce 
    function registrar_api_funda_salesforce() {
        register_rest_route('api', '/salesforce', array(
            array(
                'methods' => 'POST',
                'callback' => 'api_funda_salesforce'
            ),
        ));
    }
    add_action('rest_api_init', 'registrar_api_funda_salesforce');
    
    // Obtener token
    function registrar_api_token() {
        register_rest_route('api', '/tokentmp', array(
            array(
                'methods' => 'POST',
                'callback' => 'api_get_token'
            ),
        ));
    }
    add_action('rest_api_init', 'registrar_api_token');
    
?>