<?php
session_start();
date_default_timezone_set('America/Lima');
setlocale(LC_TIME, "spanish");

if( $_SESSION["ziel_idu"] == '' ){
    header('location: login.php');
}
#en caso sea cajero y llegue hasta aqui lo mando al login
if( $_SESSION["ziel_tipo"] != 'A' ){ header('location: login.php'); }
#
$idu = $_SESSION["ziel_idu"];
$tipoUsuarioZiel = $_SESSION["ziel_tipo"];

$rand = '';
$tag = date('d-m-y H-i-s');
$rand = '?v='.rand(0,9999999);

include_once '../php/mysql.class/mysql.class.php';
include_once '../php/clases/clsUsuario.php';
$OBJ            = new Usuario();

include_once '../js/zebra/Zebra_Pagination.php';
$zebra = new Zebra_Pagination();

$total  = 0;
$total  = $OBJ->get_count_prod_mas_vendido();
$result = 10;

$zebra->records( $total );
$zebra->records_per_page( $result );

#limit 
$limite = ' LIMIT '.( $zebra->get_page() -1 ) * $result.','.$result;

/* --------------------------------------------------- */

$tag            = sha1( $tag );
$data_clases    = array();
$data_generico  = array();
$data_prods     = array();

$data_prods     = $OBJ->rep_prods_mas_vendidos( $idu , $limite );

/**/
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Ziel - Productos</title>

    <!-- Bootstrap Core CSS -->
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="../bower_components/datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../dist/css/estilos.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    
    <!-- Alertify -->
    <link rel="stylesheet" href="../js/alertify/alertify.core.css" />
    <link rel="stylesheet" href="../js/alertify/alertify.default.css" id="toggleCSS" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

<style type="text/css">
@media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px)  {    
    /*
    Label the data
    */
    td:nth-of-type(1):before { content: "Id:"; }
    td:nth-of-type(2):before { content: ""; }
    td:nth-of-type(3):before { content: "Producto:"; }
    td:nth-of-type(4):before { content: "Clases:"; }
    td:nth-of-type(5):before { content: "Generico:"; }
    td:nth-of-type(6):before { content: "Estado"; }
}   
</style>

<script type="text/javascript">
    var _pagina = '<?php echo $zebra->get_page(); ?>';
    </script>

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <?php 
        if( $tipoUsuarioZiel == 'C' ){
            include_once('menu_cajero.php');
        }else{
            include_once('menu_nav.php');
        }
        ?>

        <div id="page-wrapper">
            
            <div class="row">
                <div class="col-lg-12">
                    <ol class="breadcrumb" >
                        <li>
                            <a href="index.php">Inicio</a>
                        </li>
                        <li class="active" >Productos más vendidos</li>
                    </ol>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="text-primary" >Productos más vendidos <small class="text-muted" >Usuario actual: <?php echo $_SESSION["ziel_nombre"]; ?></small></h4>
                </div>
                <!-- /panel-heading -->
                <div class="panel-body">
                    <table id="ItemsResponse" class="table" >
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if( is_array( $data_prods['data'] ) ){
                            foreach ( $data_prods['data'] as $key => $rs ) {
                                echo '<tr>';
                                    echo '<td>'.$rs->idp.'</td>';
                                    echo '<td>'.$rs->prod.'</td>';
                                    echo '<td>'.$rs->n.'</td>';
                                    echo '<td>'.$rs->stock.'</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php $zebra->render(); ?>
                </div>
                <!-- /panel-body -->
            </div>
        </div>
    </div>


            
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->



</div>

<!-- Popup -->
<?php include_once('popup1.php'); ?>

    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="../bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
    
    <!-- Alertify -->
    <script type="text/javascript" src="../js/alertify/alertify.min.js" ></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>
    
    <!-- Funciones de la pagina -->
    <script src="../dist/js/rep_productos_todos.js<?php echo $rand; ?>"></script>

    <!-- Funciones de mensajes -->
    <script src="../dist/js/mensajes.js<?php echo $rand; ?>"></script>

</body>

</html>
