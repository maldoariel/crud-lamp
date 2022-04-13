<?php

include 'funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

if (isset($_POST['submit'])) {
  $resultado = [
    'error' => false,
    'mensaje' => 'El Server ' . escapar($_POST['NAME']) . ' ha sido agregado con éxito'
  ];

  $config = include 'config.php';

  try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $server = [
      "NAME"   => $_POST['NAME'],
      "IPV4" => $_POST['IPV4'],
      "IPV6"    => $_POST['IPV6'],
      "SERVICIO"     => $_POST['SERVICIO'],
      "PROVEDOR"   => $_POST['PROVEDOR'],
      "ESTADO" => $_POST['ESTADO'],
      "DATACENTER"    => $_POST['DATACENTER'],
      "DETALLES"     => $_POST['DETALLES'],

    ];

    $consultaSQL = "INSERT INTO SERVERS (NAME, IPV4, IPV6, SERVICIO, PROVEDOR, ESTADO, DATACENTER, DETALLES)";
    $consultaSQL .= "values (:" . implode(", :", array_keys($server)) . ")";

    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute($server);

  } catch(PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}
?>

<?php include 'templates/header.php'; ?>

<?php
if (isset($resultado)) {
  ?>
  <div class="container mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-<?= $resultado['error'] ? 'danger' : 'success' ?>" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="mt-4">Cargar Server</h2>
      <hr>
      <form method="post">
        <div class="form-group">
          <label for="NAME">Nombre</label>
          <input type="text" name="NAME" id="NAME" class="form-control">
        </div>
        <div class="form-group">
          <label for="IPV4">IPV4</label>
          <input type="text" name="IPV4" id="IPV4" class="form-control">
        </div>
        <div class="form-group">
	  <label for="IPV6">IPV6</label>
          <input type="text" name="IPV6" id="IPV6" class="form-control">
        </div>
        <div class="form-group">
          <label for="SERVICIO">Servicio(DNS,DHCP, DB, etc)</label>
          <input type="text" name="SERVICIO" id="SERVICIO" class="form-control">
	</div>
	<div class="form-group">
          <label for="PROVEDOR">Proveedor(AXIROS,INTRAWAY,etc)</label>
          <input type="text" name="PROVEDOR" id="PROVEDOR" class="form-control">
        </div>
        <div class="form-group">
          <label for="ESTADO">Estado(PRODUCCION, MAQUETA,etc)</label>
          <input type="text" name="ESTADO" id="ESTADO" class="form-control">
        </div>
        <div class="form-group">
          <label for="DATACENTER">Datacenter</label>
          <input type="text" name="DATACENTER" id="DATACENTER" class="form-control">
        </div>
        <div class="form-group">
	  <!--<label for="DETALLES">Detalles</label>-->
          <textarea name="DETALLES" id="DETALLES" rows="5" cols="80" maxlength="80">Escribe aquí tus comentarios... </textarea>
         <!-- <input type="text" name="DETALLES" id="DETALLES" class="form-control">-->
        </div>
        <div class="form-group">
          <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
          <input type="submit" name="submit" class="btn btn-primary" value="Enviar">
          <a class="btn btn-primary" href="index.php">Regresar al inicio</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>
