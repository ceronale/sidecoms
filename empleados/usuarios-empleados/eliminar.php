<?php
include( '../../layouts/session.php' );
include_once 'class.crud.php';
$crud = new crud();

$id = $_GET['id'];
if($eliminar_empleado = $crud->eliminar_empleado($id))
{
    header("Location:  ../usuarios_empleados/");
}
else
{
    header("Location:  ../usuarios_empleados/?failure");
}