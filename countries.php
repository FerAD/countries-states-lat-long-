<?php
	// Conectando, seleccionando la base de datos
	$link = mysql_connect('192.168.10.10:3306', 'homestead', 'secret')
    	or die('No se pudo conectar: ' . mysql_error());
	
	echo 'Connected successfully';
	mysql_select_db('CountriesAndStates') or die('No se pudo seleccionar la base de datos');

	// Realizar una consulta MySQL
	$query1 = 'SELECT * FROM states WHERE id >= 2183 ';
	$result = mysql_query($query1) or die('Consulta fallida: ' . mysql_error());
	$counter = 0;

	while($state = mysql_fetch_assoc($result)){
		$query2 = "SELECT * FROM countries WHERE id = {$state['id_country']}";
		$result2 = mysql_query($query2) or die('Consulta fallida: ' . mysql_error());
		$country = mysql_fetch_assoc($result2);

		$state_name = str_replace(" ", "+", $state['name']);
		$url = "https://maps.google.com/maps/api/geocode/json?address=".$state_name."&key=AIzaSyBWaB5ED6OIFNjYbsAh-CLbSWgNRXGMPPU&sensor=false&region=".$country['sortname'];
		$response = file_get_contents($url);
		$response = json_decode($response, true);
	
		$lat = $response['results'][0]['geometry']['location']['lat'];
		$long = $response['results'][0]['geometry']['location']['lng'];

		$query3 = "UPDATE states SET latitude ='{$lat}', longitude = '{$long}' WHERE id = {$state['id']}";
		$result3 = mysql_query($query3) or die('Consulta fallida: ' . mysql_error());

		$counter++;
		echo $counter." ".$state_name." ".$country['sortname']." ".$lat." ".$long;
		echo "<br>";
	}
	// Liberar resultados
	mysql_free_result($result);



?>