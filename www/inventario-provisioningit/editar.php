<?php
include 'funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

$config = include 'config.php';

$resultado = [
  'error' => false,
  'mensaje' => ''
];

if (!isset($_GET['id'])) {
  $resultado['error'] = true;
  $resultado['mensaje'] = 'El Servidor no existe';
}

if (isset($_POST['submit'])) {
  try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $server = [
      "ID"        => $_GET['id'],
      "NAME"    => $_POST['NAME'],
      "IPV4"  => $_POST['IPV4'],
      "IPV6"     => $_POST['IPV6'],
      "SERVICIO"      => $_POST['SERVICIO'],
      "PROVEDOR"        => $_POST['PROVEDOR'],
      "ESTADO"    => $_POST['ESTADO'],
      "DATACENTER"  => $_POST['DATACENTER'],
      "DETALLES"  => $_POST['DETALLES']
    ];
    
    $consultaSQL = "UPDATE SERVERS SET
        NAME = :NAME,
        IPV4 = :IPV4,
        IPV6 = :IPV6,
	SERVICIO = :SERVICIO,
        PROVEDOR = :PROVEDOR,
        ESTADO = :ESTADO,
        DATACENTER = :DATACENTER,
        DETALLES = :DETALLES,
        updated_at = NOW()
        WHERE ID = :ID";
    $consulta = $conexion->prepare($consultaSQL);
    $consulta->execute($server);

  } catch(PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}

try {
  $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
  $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);
    
  $id = $_GET['id'];
  $consultaSQL = "SELECT * FROM SERVERS WHERE id =" . $id;

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $server = $sentencia->fetch(PDO::FETCH_ASSOC);

  if (!$server) {
    $resultado['error'] = true;
    $resultado['mensaje'] = 'No se ha encontrado el Server';
  }

} catch(PDOException $error) {
  $resultado['error'] = true;
  $resultado['mensaje'] = $error->getMessage();
}
?>

<?php require "templates/header.php"; ?>

<?php
if ($resultado['error']) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($_POST['submit']) && !$resultado['error']) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          El Server ha sido actualizado correctamente
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($server) && $server) {
  ?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="mt-4">Editando el Server <?= escapar($server['NAME']) . ' ' . escapar($server['SERVICIO'])  ?></h2>
        <hr>
        <form method="post">
          <div class="form-group">
            <label for="NAME">Nombre</label>
            <input type="text" name="NAME" id="NAME" value="<?= escapar($server['NAME']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="IPV4">IPV4</label>
            <input type="text" name="IPV4" id="IPV4" value="<?= escapar($server['IPV4']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="IPV6">IPV6</label>
            <input type="text" name="IPV6" id="IPV6" value="<?= escapar($server['IPV6']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="SERVICIO">Servicio(DNS,DHCP, DB, etc)</label>
            <input type="text" name="SERVICIO" id="SERVICIO" value="<?= escapar($server['SERVICIO']) ?>" class="form-control">
	  </div>
         <div class="form-group">
            <label for="PROVEDOR">Proveedor(AXIROS,INTRAWAY,etc)</label>
            <input type="text" name="PROVEDOR" id="PROVEDOR" value="<?= escapar($server['PROVEDOR']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="ESTADO">Estado(PRODUCCION, MAQUETA,etc)</label>
            <input type="text" name="ESTADO" id="ESTADO" value="<?= escapar($server['ESTADO']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="DATACENTER">Datacenter</label>
            <input type="text" name="DATACENTER" id="DATACENTER" value="<?= escapar($server['DATACENTER']) ?>" class="form-control">
          </div>
	  <div class="form-group">
            <textarea name="DETALLES" id="DETALLES" rows="5" cols="80" maxlength="80"><?= escapar($server['DETALLES']) ?></textarea>
            <!--<label for="DETALLES">Detalles</label>
            <input type="text" name="DETALLES" id="DETALLES" value="<?#= escapar($server['DETALLES']) ?>" class="form-control">-->
          </div>
          <div class="form-group">
            <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
            <input type="submit" name="submit" class="btn btn-primary" value="Actualizar">
            <a class="btn btn-primary" href="index.php">Regresar al inicio</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php require "templates/footer.php"; ?>
