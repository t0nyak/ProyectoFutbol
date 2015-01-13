<?php

$host = 'localhost';
$DBuser = 'root';
$DBname = 'nombresrandom';
$DBpass = '';

$db = mysql_connect($host, $DBuser, $DBpass) or die(mysql_error());  
mysql_select_db($DBname, $db) or die(mysql_error());

generaPartidos();

function generaPartidos(){
	$query = "SELECT * FROM ligas";
	$query = mysql_query($query);
	while($fila = mysql_fetch_array($query)){
		$query2 = "SELECT * FROM clasificaciones WHERE idLiga = ".$fila['id']."";
		$query2 = mysql_query($query2);
		$equipos = array();
		while($fila2 = mysql_fetch_array($query2)){
			$equipos[] = $fila2;
		}
		$hola = array_slice($equipos,1);
		generaPartidos2($equipos[0]['idEquipo'],array_slice($equipos,1));
	}
}

function generaPartidos2($equipo,$grupo){
	if(count($grupo)==1){
		$resultado = juegaPartido($equipo,$grupo[0]['idEquipo']);
		$query2 = "INSERT INTO partidos (equipo1, equipo2,resultado) VALUES ('".$equipo."','".$grupo[0]['idEquipo']."','".$resultado."')";
		$query2 = mysql_query($query2);
		$resultado = juegaPartido($grupo[0]['idEquipo'],$equipo);
		$query2 = "INSERT INTO partidos (equipo1, equipo2,resultado) VALUES ('".$grupo[0]['idEquipo']."','".$equipo."','".$resultado."')";
		$query2 = mysql_query($query2);
	}else{
		$resultado = juegaPartido($equipo,$grupo[0]['idEquipo']);
		$query2 = "INSERT INTO partidos (equipo1, equipo2,resultado) VALUES ('".$equipo."','".$grupo[0]['idEquipo']."','".$resultado."')";
		$query2 = mysql_query($query2);
		$resultado = juegaPartido($grupo[0]['idEquipo'],$equipo);
		$query2 = "INSERT INTO partidos (equipo1, equipo2,resultado) VALUES ('".$grupo[0]['idEquipo']."','".$equipo."','".$resultado."')";
		$query2 = mysql_query($query2);
		generaPartidos2($equipo,array_slice($grupo,1));
		generaPartidos2($grupo[0]['idEquipo'],array_slice($grupo,1));
	}
}

function juegaPartido($equipo1,$equipo2){
	$A = 0;
	$B = 0;
	for($i=1;$i<181;$i++){
		if(($i%18) === 0){
			$jugA = jugadorAleatorio($equipo1);
			$jugB = jugadorAleatorio($equipo2);
			$attr = rand(1,20);
			$res = rand(0,$jugA[$attr]+$jugB[$attr]);
			if($res<$jugA[$attr]){
				if(($jugA[$attr]-$res)>($jugB[$attr]/2)){
					$A++;
				}
			}else{
				if((($jugA[$attr]+$jugB[$attr])-$res)>($jugA[$attr]/2)){
					$B++;
				}
			}
		}
	}
	return $A.' - '.$B;
}

function jugadorAleatorio($equipo){
	$query3 = "SELECT * FROM federados WHERE idEquipo = $equipo";
	$query3 = mysql_query($query3);
	$jugadores = array();
	while($fila3 = mysql_fetch_array($query3)){
		$jugadores[] = $fila3;
	}
	return $jugadores[rand(1,11)];
}

function creaDivisiones(){
	mysql_query("SET NAMES 'utf8'");
	
	$ciudades = array(1 => 'Madrid',2 => 'Barcelona',3 => 'Valencia',4 => 'Sevilla',5 => 'Zaragoza',6 => 'Málaga',7 => 'Murcia',8 => 'Palma de Mallorca',9 => 'Las Palmas de Gran Canaria',10 => 'Bilbao',11 => 'Alicante',12 => 'Córdoba',13 => 'Valladolid',14 => 'Vigo',15 => 'Gijón',16 => 'Hospitalet de Llobregat',17 => 'La Coruña',18 => 'Vitoria',19 => 'Granada',20 => 'Elche',21 => 'Oviedo',22 => 'Badalona',23 => 'Cartagena',24 => 'Tarrasa',25 => 'Jerez de la Frontera',26 => 'Sabadell',27 => 'Santa Cruz de Tenerife',28 => 'Móstoles',29 => 'Alcalá de Henares',30 => 'Fuenlabrada',31 => 'Pamplona',32 => 'Almería',33 => 'Leganés',34 => 'San Sebastián',35 => 'Castellón de la Plana',36 => 'Burgos',37 => 'Santander',38 => 'Albacete',39 => 'Getafe',40 => 'Alcorcón',41 => 'Logroño',42 => 'San Cristobal de la Laguna',43 => 'Badajoz',44 => 'Salamanca',45 => 'Huelva',46 => 'Marbella',47 => 'Lérida',48 => 'Tarragona',49 => 'León',50 => 'Dos Hermanas',51 => 'Torrejón de Ardoz',52 => 'Parla',53 => 'Mataró',54 => 'Cádiz',55 => 'Santa Coloma de Gramanet',56 => 'Algeciras',57 => 'Jaén',58 => 'Alcobendas',59 => 'Orense',60 => 'Reus',61 => 'Torrevieja',62 => 'Telde',63 => 'Baracaldo');
	for($i = 0;$i< 63;$i++){
		$query = "INSERT INTO ligas (lugar, division) VALUES ('".$ciudades[$i+1]."','primera')";
		$query2 = "INSERT INTO ligas (lugar, division) VALUES ('".$ciudades[$i+1]."','segunda')";
		$query3 = "INSERT INTO ligas (lugar, division) VALUES ('".$ciudades[$i+1]."','tercera')";
		mysql_query($query);
		mysql_query($query2);
		mysql_query($query3);
	}
}

function insertaEquipos(){
	mysql_query("SET NAMES 'utf8'");

	$query2 = 'SELECT * FROM ligas';
	$query2 = mysql_query($query2);
	$vuelta = 1;
	while($fila2 = mysql_fetch_array($query2)){
		$query = "SELECT * FROM equipos WHERE origen LIKE '".$fila2['lugar']."'";
		$query = mysql_query($query);
		$equiposS = array();
		while($fila = mysql_fetch_array($query)){
			$equiposS[] = $fila;
		}
		if($vuelta === 2){
			$vuelta = 0;
		}else{
			$vuelta++;
		}
		$i = 0;
		while($i<20){
			$consulta = "INSERT INTO clasificaciones (idEquipo, idLiga) VALUES ('".$equiposS[$i+(20*$vuelta)]['id']."','".$fila2['id']."')";
			mysql_query($consulta);
			$i++;
		}
	}
}

function creaJugadores(){
	$query = 'SELECT * FROM fakenames';
	$query = mysql_query($query);
	
	mysql_query("SET NAMES 'utf8'");
	while($fila = mysql_fetch_array($query)){
		$atributos = array(
						"velocidad" => 0,
						"agilidad" => 0,
						"resistencia" => 0,
						"tecnica" => 0,
						"regate" => 0,
						"trabajo_en_equipo" => 0,
						"talento" => 0,
						"vision" => 0,
						"marcaje" => 0,
						"centros" => 0,
						"disparos" => 0,
						"fuerza" => 0,
						"salto" => 0,
						"colocacion" => 0,
						"equilibrio" => 0,
						"saques" => 0,
						"decisiones" => 0,
						"serenidad" => 0,
						"juego_aereo" => 0,
						"anticipacion" => 0,
						);	
		for($j = 0;$j<50;$j++){
			$atributo = rand(1,20);
			if($atributo === 1){
				$atributo = 'velocidad';
			}else if($atributo === 2){
				$atributo = 'agilidad';
			}else if($atributo === 3){
				$atributo = 'resistencia';
			}else if($atributo === 4){
				$atributo = 'tecnica';
			}else if($atributo === 5){
				$atributo = 'regate';
			}else if($atributo === 6){
				$atributo = 'trabajo_en_equipo';
			}else if($atributo === 7){
				$atributo = 'talento';
			}else if($atributo === 8){
				$atributo = 'vision';
			}else if($atributo === 9){
				$atributo = 'marcaje';
			}else if($atributo === 10){
				$atributo = 'centros';
			}else if($atributo === 11){
				$atributo = 'disparos';
			}else if($atributo === 12){
				$atributo = 'fuerza';
			}else if($atributo === 13){
				$atributo = 'salto';
			}else if($atributo === 14){
				$atributo = 'colocacion';
			}else if($atributo === 15){
				$atributo = 'equilibrio';
			}else if($atributo === 16){
				$atributo = 'saques';
			}else if($atributo === 17){
				$atributo = 'decisiones';
			}else if($atributo === 18){
				$atributo = 'serenidad';
			}else if($atributo === 19){
				$atributo = 'juego_aereo';
			}else if($atributo === 20){
				$atributo = 'anticipacion';
			}
			$valor = rand(1,20);
			$atributos[$atributo] = $atributos[$atributo]+$valor;
		}
		$mediaPOR = ($atributos['velocidad']+ $atributos['agilidad']+$atributos['resistencia']+$atributos['tecnica']+ $atributos['regate'])/5;
		$mediaDEF = ($atributos['trabajo_en_equipo']+ $atributos['talento']+$atributos['vision']+$atributos['marcaje']+ $atributos['centros'])/5;
		$mediaCEN = ($atributos['disparos']+ $atributos['fuerza']+$atributos['salto']+$atributos['colocacion']+ $atributos['equilibrio'])/5;
		$mediaDEL = ($atributos['saques']+ $atributos['decisiones']+$atributos['serenidad']+$atributos['juego_aereo']+ $atributos['anticipacion'])/5;
		$posicion_nat = 'PORTERO';
		$media = $mediaPOR;
		if($mediaDEF>$mediaPOR || $mediaDEL>$mediaPOR || $mediaCEN>$mediaPOR){
			if($mediaCEN>$mediaDEF || $mediaDEL>$mediaDEF){
				if($mediaDEL>$mediaCEN){
					$posicion_nat = 'DELANTERO';
					$media = $mediaDEL;
				}else{
					$posicion_nat = 'CENTRAL';
					$media = $mediaCEN;
				}
			}else{
				$posicion_nat = 'DEFENSA';
				$media = $mediaDEF;
			}
		}
		$query2 = 'INSERT INTO jugadores (idNombre,velocidad,agilidad,resistencia,tecnica,regate,trabajo_en_equipo,talento,
						vision,marcaje,centros,disparos,fuerza,salto,colocacion,equilibrio,saques,decisiones,serenidad,juego_aereo,anticipacion,sueldo,dinero,posicion_natural,avg_pos_nat)
						VALUES ('.$fila['number'].','.$atributos["velocidad"].','.$atributos["agilidad"].','.$atributos["resistencia"].','.$atributos["tecnica"].','.$atributos["regate"].','.$atributos["trabajo_en_equipo"].','.$atributos["talento"].','.$atributos["vision"].','.$atributos["marcaje"].','.$atributos["centros"].','.$atributos["disparos"].','.$atributos["fuerza"].','.$atributos["salto"].','.$atributos["colocacion"].','.$atributos["equilibrio"].','.$atributos["saques"].','.$atributos["decisiones"].','.$atributos["serenidad"].','.$atributos["juego_aereo"].','.$atributos["anticipacion"].',100,200,"'.$posicion_nat.'",'.$media.')';
		if(mysql_query($query2)){
			echo '<br />Introducido jugador '.$fila['number'].' con la consulta:<br />'.$query2;
		}else{
			echo '<br />Hubo algun problema con la consulta: '.$query2;
		}
	}
}

function distribuyeEquipos(){
	$query = 'SELECT * FROM jugadores';
$query = mysql_query($query);
$jugadores = array();

while($fila = mysql_fetch_array($query)){
	$jugadores[] = $fila;
}

shuffle($jugadores);
$indice = 0;
$ciudades = array(1 => 'Madrid',2 => 'Barcelona',3 => 'Valencia',4 => 'Sevilla',5 => 'Zaragoza',6 => 'Málaga',7 => 'Murcia',8 => 'Palma de Mallorca',9 => 'Las Palmas de Gran Canaria',10 => 'Bilbao',11 => 'Alicante',12 => 'Córdoba',13 => 'Valladolid',14 => 'Vigo',15 => 'Gijón',16 => 'Hospitalet de Llobregat',17 => 'La Coruña',18 => 'Vitoria',19 => 'Granada',20 => 'Elche',21 => 'Oviedo',22 => 'Badalona',23 => 'Cartagena',24 => 'Tarrasa',25 => 'Jerez de la Frontera',26 => 'Sabadell',27 => 'Santa Cruz de Tenerife',28 => 'Móstoles',29 => 'Alcalá de Henares',30 => 'Fuenlabrada',31 => 'Pamplona',32 => 'Almería',33 => 'Leganés',34 => 'San Sebastián',35 => 'Castellón de la Plana',36 => 'Burgos',37 => 'Santander',38 => 'Albacete',39 => 'Getafe',40 => 'Alcorcón',41 => 'Logroño',42 => 'San Cristobal de la Laguna',43 => 'Badajoz',44 => 'Salamanca',45 => 'Huelva',46 => 'Marbella',47 => 'Lérida',48 => 'Tarragona',49 => 'León',50 => 'Dos Hermanas',51 => 'Torrejón de Ardoz',52 => 'Parla',53 => 'Mataró',54 => 'Cádiz',55 => 'Santa Coloma de Gramanet',56 => 'Algeciras',57 => 'Jaén',58 => 'Alcobendas',59 => 'Orense',60 => 'Reus',61 => 'Torrevieja',62 => 'Telde',63 => 'Baracaldo');

while(($indice+1)*11<count($jugadores)){
	$indice++;
	mysql_query("SET NAMES 'utf8'");
	$query = "INSERT INTO equipos (nombre, origen) VALUES ('Equipo".$indice."','".$ciudades[rand(1,63)]."')";
	$query = mysql_query($query);
	$query = "SELECT * FROM equipos WHERE nombre = 'Equipo".$indice."'";
	$query = mysql_query($query);
	$fila = mysql_fetch_array($query);
	$media = 0;

	for($i = 0;$i<11;$i++){
		$jugador = $jugadores[($indice-1)*11+$i];
		$query = "INSERT INTO federados (idEquipo,idJugador) VALUES ('".$fila['id']."','".$jugador['idNombre']."')";
		$query = mysql_query($query);
		$media = $media + $jugador['avg_pos_nat'];
	}
	$media = $media/11;
	
	$query = "UPDATE equipos SET media = '$media' WHERE id = '".$fila['id']."'";
	$query = mysql_query($query);
	
	$query = "SELECT * FROM federados WHERE idEquipo = ".$fila['id']."";
	$query = mysql_query($query);
	
	echo 'Confeccionado equipo '.$indice.' con:<br />';
	
	while($fila = mysql_fetch_array($query)){
		$query2 = "SELECT * FROM fakenames WHERE number = ".$fila['idJugador']."";
		$query2 = mysql_query($query2);
		$fila2 = mysql_fetch_array($query2);
		echo '-> '.utf8_decode($fila2['givenname']).' '.utf8_decode($fila2['surname']).'<br />';
	}
	
	
}
}


?>
