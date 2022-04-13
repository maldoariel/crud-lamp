<?php
include 'funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

$error = false;
$config = include 'config.php';

try {
  $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
  $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

  if (isset($_POST['SERVICIO'])) {
    $consultaSQL = "SELECT * FROM SERVERS WHERE SERVICIO LIKE '%" . $_POST['SERVICIO'] . "%'";
  } else {
    $consultaSQL = "SELECT * FROM SERVERS";
  }

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $servers = $sentencia->fetchAll();

} catch(PDOException $error) {
  $error= $error->getMessage();
}

$titulo = isset($_POST['SERVICIO']) ? 'Lista de Servidores (' . $_POST['SERVICIO'] . ')' : 'Lista de Servidores';
?>

<?php include "templates/header.php"; ?>

<?php
if ($error) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $error ?>
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
      <a href="crear.php"  class="btn btn-primary mt-4">Cargar Servidor</a>
      <hr>
      <form method="post" class="form-inline">
        <div class="form-group mr-3">
          <input type="text" id="SERVICIO" name="SERVICIO" placeholder="Buscar por Servicio" class="form-control">
        </div>
        <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
        <button type="submit" name="submit" class="btn btn-primary">Ver resultados</button>
      </form>
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="mt-3"><?= $titulo ?></h2>
      <table class="table">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>IPv4</th>
            <th>Ipv6</th>
            <th>Servicio</th>
            <th>Proveedor</th>
	    <th>Estado</th>
	    <th>Datacenter</th>
            <th>Detalles</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($servers && $sentencia->rowCount() > 0) {
            foreach ($servers as $fila) {
              ?>
              <tr>
                <td><?php echo escapar($fila["NAME"]); ?></td>
                <td><?php echo escapar($fila["IPV4"]); ?></td>
                <td><?php echo escapar($fila["IPV6"]); ?></td>
                <td><?php echo escapar($fila["SERVICIO"]); ?></td>
		<td><?php echo escapar($fila["PROVEDOR"]); ?></td>
                <td><?php echo escapar($fila["ESTADO"]); ?></td>
                <td><?php echo escapar($fila["DATACENTER"]); ?></td>
                <td><?php echo escapar($fila["DETALLES"]); ?></td>

                <td>
                  <a onclick="AlertDel();" href="<?= 'borrar.php?id=' . escapar($fila["ID"]) ?>">🗑️Borrar</a>
                  <a href="<?= 'editar.php?id=' . escapar($fila["ID"]) ?>">✏️Editar</a>
                </td>
              </tr>
              <?php
            }
          }
          ?>
        <tbody>
      </table>
    </div>
  </div>
</div>

<?php include "templates/footer.php"; ?>
