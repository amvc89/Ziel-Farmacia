<?php
date_default_timezone_set('America/Lima');
setlocale(LC_TIME, "spanish");

include_once '../mysql.class/mysql.class.php';
include_once '../clases/clsUsuario.php';


$nombre_producto 	= '';
$lote_producto 		= '';
$fec_producto 		= '';
#
$compra_producto 	= '';
$venta_producto 	= '';
$utilidad_producto 	= '';



$nombre_producto 	= $_POST["irProd"];
$lote_producto 		= $_POST["irLote"];
$fec_producto 		= $_POST["irVence"];
#
$cant_producto 		= $_POST["irCant"];
$tipo_producto 		= $_POST["irTipo"];
#
$compra_producto 	= $_POST["irVenta"];
$venta_producto 	= $_POST["irVenta"];
$utilidad_producto 	= 0;
$tabActive 			= 2;

#Un nuevo producto
$error = array();
$response['error'] = 'si';
$response['existe'] = 'si';

$vencimiento 	= '';
$arr_fecha = array();
if( $_POST["irVence"] != '' ){
	$arr_fecha = split('/', $_POST["irVence"] );
	$vencimiento 	= $arr_fecha[2].'-'.$arr_fecha[1].'-'.$arr_fecha[0];
}

$OBJ = array();
$OBJ = new Usuario();

#Creando el producto



#Insert
# ===================================================== 
#Se guardan 3 enlaces.
$OBJ->set_Valor( '1' , 'int_IdClase' );
$OBJ->set_Valor( '1' , 'int_IdGenerico' );
$OBJ->set_Valor( $nombre_producto , 'var_Nombre' );
$OBJ->set_Valor( 'Generico' , 'tipo' );
$OBJ->set_Valor( '1' , 'int_Cantidad' );
$OBJ->set_Valor( '1' , 'int_IdUM' );
$OBJ->set_Valor( '0' , 'chr_Destacado' );
$OBJ->set_Valor( '' , 'var_Laboratorio' );
$OBJ->set_Valor( '1' , 'int_IdProveedor' );
$OBJ->set_Valor( '' , 'var_Proveedor' );
#Guardando Producto
$id_producto = $OBJ->insert_Producto();
$response['idprod'] = $id_producto;

#Existe el producto?
$existe_prod = $OBJ->existe_producto( $txtNombre );
if( $existe_prod != 0 ){
	echo json_encode( $response );
	die(0);
}

# Clases de Producto 
$OBJc = array();
$OBJc = new Usuario();
#
$OBJc->set_Dato( $id_producto , 'int_IdProducto' );
$OBJc->set_Dato( '1' , 'int_IdClase' );
$OBJc->insert_clases_in_producto();

#Equivalencia de Producto ++++++++++++++++++++++++++++
$OBJ = array();
$OBJ = new Usuario();

$OBJ = new Usuario();
$OBJ->set_Dato( 'PRI' , 'chr_Estado' );
$OBJ->set_Dato( '1' , 'int_IdUM' );
$OBJ->set_Dato( '1' , 'int_Cantidad' );
$OBJ->set_Dato( $id_producto , 'int_IdProducto' );
$OBJ->insert_equivalencia_Producto();


# Los almacenes 
$OBJa = array();
$OBJa = new Usuario();
#
$OBJa->set_Dato( $id_producto , 'int_IdProducto' );
$OBJa->set_Dato( '1' , 'int_IdAlmacen' );
$OBJa->insert_clases_in_producto();


# Precio de producto y sus equivalencias
$OBJ = array();
$OBJ = new Usuario();

#Anulando productos anteriores.
$OBJ->anular_precios_producto( $id_producto );

$data_eqv = array();
$um = '';

$newprecio = 0;
$newcompra = 0;

$data_eqv = $OBJ->get_equivalencias_producto_for_precio( $id_producto );

$um 	= 1;
$cant 	= $cant_producto;
#-------------------------------------------
$OBJ->set_Valor( $id_producto , 'int_IdProducto' );
$OBJ->set_Valor( $um , 'int_IdUM' );
$OBJ->set_Valor( $tabActive , 'int_TipoCalculo' );

#Calculo por precio de venta
$OBJ->set_Valor( $venta_producto , 'flt_Precio_Compra' );
$OBJ->set_Valor( $venta_producto , 'ftl_Precio_Venta' );

$OBJ->set_Valor( $utilidad_producto , 'flt_Utilidad' );

#Guardando Precio Producto
$OBJ->insert_Precio_Producto();

#Actualizo el precio del productos en las promociones.
$response['promo'] = $OBJc->update_precio_promo_prod( $id_producto );

/*


#ENCABEZADO PARTE DE ENTRADA
$OBJ = array();
$OBJ = new Usuario();

$hoy = date("Y-m-d");

$OBJ->set_Valor( '1' , 'int_IdProveedor' );
$OBJ->set_Valor( $hoy , 'dt_Fecha' );
$OBJ->set_Valor( $compra_producto , 'flt_Total' );

if( $idPE == 0 ){
	$idPE = $OBJ->insert_pe();
}else{
	$OBJ->set_Union( $idPE , 'int_IdParteEntrada' );
	$OBJ->update_pe();
}

$response['idPE'] = $idPE;

$OBJ1 = new Usuario();
$OBJ1->set_Dato( $idPE , 'int_IdParteEntrada' );
$OBJ1->set_UnionT( $tag , 'txt_Tag' );
$response['sql'] = $OBJ1->join_pe_detalle();

#DETALLE PARTE DE ENTRADA
$OBJ->set_Dato( $id_producto , 'int_IdProducto' );
$OBJ->set_Dato( '1' , 'int_IdUnidadMedida' );
$OBJ->set_Dato( '1' , 'int_Cantidad' );
$OBJ->set_Dato( $compra_producto , 'flt_Precio_Compra' );
$OBJ->set_Dato( $venta_producto , 'ftl_Precio_Venta' );
$OBJ->set_Dato( $utilidad_producto , 'flt_Utilidad' );
$OBJ->set_Dato( $compra_producto , 'flt_Precio' );
$OBJ->set_Dato( $compra_producto , 'flt_Total' );
$OBJ->set_Dato( '' , 'txt_Tag' );
$OBJ->set_Dato( $lote_producto , 'var_Lote' );
$OBJ->set_Dato( $vencimiento , 'dt_Vencimiento' );
$OBJ->set_Dato( '' , 'var_Laboratorio' );
# Actualizar, Insertar
$response['id'] = $OBJ->insert_detalle_pe();


/**/


#Ahora lo agrego al detalle de la venta y envio la data.

echo json_encode($response);



?>
