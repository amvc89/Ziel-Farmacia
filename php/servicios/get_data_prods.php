<?php
include_once '../mysql.class/mysql.class.php';
include_once '../clases/clsUsuario.php';

$OBJ 		= new Usuario();
$response 	= array();

$q = '';

$q = $_GET["q"];

$response = $OBJ->get_all_productos_filtro( $q );


echo json_encode($response);


?>