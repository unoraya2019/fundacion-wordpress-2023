<?php
    // Función que se encarga de recuperar los datos de la API externa
    function get_data_paises($token) {
        $uri = "https://funbolivar--promusico.my.salesforce.com/services/data/v51.0/query/?";
        $queryParams = "q=SELECT+Name,ID,FBDX_Activo__c+FROM+FBDX_Poblacion__c+WHERE+FBDX_Activo__c+=+true+and+FBDX_EsPais__c+=+true";
        $url = $uri.$queryParams;
        $response = wp_remote_get( $url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ),
        ));
        if (is_wp_error($response)) {
    		error_log("Error: ". $response->get_error_message());
    		return false;
    	}
    	$body = wp_remote_retrieve_body($response);
    	$res = json_decode($body);
    	$data = $res->records;
    	return $data;
    }
    
    function get_data_depto($token, $pais) {
        $uri = "https://funbolivar--promusico.my.salesforce.com/services/data/v51.0/query?";
        $query = "q=SELECT+Name,ID,FBDX_Activo__c+FROM+FBDX_Poblacion__c+WHERE+FBDX_Activo__c+=+true+and+FBDX_EsDepartamento__c+=+true+and+FBD_PoblacionPrincipal__c+=+'".$pais."'";
        $url = $uri.$query;
        $response = wp_remote_get( $url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ),
        ));
        if (is_wp_error($response)) {
    		error_log("Error: ". $response->get_error_message());
    		return false;
    	}
    	$body = wp_remote_retrieve_body($response);
    	$res = json_decode($body);
    	$data = $res->records;
    	return $data;
    }
    
    function get_data_municipio($token, $depto) {
        $uri = "https://funbolivar--promusico.my.salesforce.com/services/data/v51.0/query/?q=";
        $query = "SELECT+Name,ID,FBDX_Activo__c+FROM+FBDX_Poblacion__c+WHERE+FBDX_Activo__c+=+true+and+FBDX_EsCiudad__c+=+true+and+FBD_PoblacionPrincipal__c+=+'".$depto."'";
        $url = $uri.$query;
        $response = wp_remote_get( $url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ),
        ));
        if (is_wp_error($response)) {
    		error_log("Error: ". $response->get_error_message());
    		return false;
    	}
    	$body = wp_remote_retrieve_body($response);
    	$res = json_decode($body);
    	$data = $res->records;
    	return $data;
    }
    
    function api_get_paises($req) {
        $key  = $req->get_header('token');
    	$content = get_data_paises($key);
    	return $content;
    }
    
    function api_get_depto($req) {
        $key  = $req->get_header('token');
        $pais  = $req->get_header('pais');
    	$content = get_data_depto($key, $pais);
    	return $content;
    }
    
    function api_get_municipio($req) {
        $key  = $req->get_header('token');
        $depto  = $req->get_header('depto');
    	$content = get_data_municipio($key, $depto);
    	return $content;
    }
    
    // Obtener el listado de Paises
    function registrar_api_get_paises() {
        register_rest_route('api', '/paises', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_get_paises'
            ),
        ));
    }

    add_action('rest_api_init', 'registrar_api_get_paises');
    
    // Obtener el listado de Deptos
    function registrar_api_get_deptos() {
        register_rest_route('api', '/deptos', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_get_depto'
            ),
        ));
    }

    add_action('rest_api_init', 'registrar_api_get_deptos');
    
    // Obtener el listado de Municipios
    function registrar_api_get_municipios() {
        register_rest_route('api', '/municipios', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_get_municipio'
            ),
        ));
    }

    add_action('rest_api_init', 'registrar_api_get_municipios');
?>