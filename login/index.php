<?php
session_start();
require_once("class.crud.php");
$error = "";
$login = new crud();

/*
if($login->is_loggedin()!="")
{

    if($_SESSION['user_type'] == 1)
    {
        $login->redirect('../usuarios');
    }
    if($_SESSION['user_type'] == 2)
    {
        $login->redirect('../clientes');
    }	

}
*/

if (isset($_POST['btn-login'])) {
    $uname = strip_tags($_POST['email']);
    $upass = strip_tags($_POST['password']);
    $upass = $login->encryption($upass);
    $loguear = $login->doLogin($uname, $upass);
    $errorMessage = '';
    $successMessage = '';

    if ($loguear == 1) {
        // Inicio de sesión exitoso
        $successMessage = "Inicio de sesión exitoso.";
        // You can redirect here instead of echoing the success message
        // $login->redirect('facebook.com');
    } elseif ($loguear == 2) {
        // Contraseña incorrecta
        $errorMessage = "Contraseña incorrecta. Por favor, inténtalo nuevamente.";
    } elseif ($loguear == 3) {
        // Nombre de usuario no encontrado
        $errorMessage = "Nombre de usuario no encontrado. Por favor, registra una cuenta.";
    } else {
        // Otro error desconocido
        $errorMessage = "Ocurrió un error al iniciar sesión.";
    }

    if ($errorMessage) {
        echo '
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>' . $errorMessage . '</strong>
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>';
    }

    if ($successMessage) {
        echo '
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>' . $successMessage . '</strong>
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>';
    }
}



?>

<?php include '../layouts/header.php'; ?>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet" />

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

<link rel="stylesheet" href="css/login.css" />
</head>

<body>
    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-5">
                    <div class="wrap">
                        <div class="img" style="background-image: url(../assets/img/login/logov1.jpg)"></div>
                        <div class="login-wrap p-4 p-md-5">
                            <div class="d-flex">
                                <div class="w-100">
                                    <h3 class="mb-4">Iniciar sesión</h3>
                                </div>
                                <div class="w-100">
                                    <p class="social-media d-flex justify-content-end">
                                        <a href="#" class="social-icon d-flex align-items-center justify-content-center"><span class="fa fa-facebook"></span></a>
                                        <a href="#" class="social-icon d-flex align-items-center justify-content-center"><span class="fa fa-twitter"></span></a>
                                    </p>
                                </div>
                            </div>

                            <form method="post" class="signin-form">
                                <div class="form-group mt-3">
                                    <input name="email" id="email" type="text" class="form-control" required />
                                    <label class="form-control-placeholder" for="username">Usuario</label>
                                </div>
                                <div class="form-group">
                                    <input name="password" id="password" type="password" class="form-control" required />
                                    <label class="form-control-placeholder" for="password">Contraseña</label>
                                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                </div>
                                <div class="form-group">

                                    <button type="submit" name="btn-login" id="login" class="form-control btn btn-primary rounded submit px-3">
                                        Iniciar sesión
                                    </button>
                                </div>
                                <div class="form-group d-md-flex">
                                    <div class="w-50 text-left">
                                        <label class="checkbox-wrap checkbox-primary mb-0">Recuerdame
                                            <input type="checkbox" checked />
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="w-50 text-md-right">
                                        <a href="#">¿Olvidaste tu contraseña?</a>
                                    </div>
                                </div>
                            </form>

                            <p class="text-center">
                                No estas registrado? <a data-toggle="tab" href="/sidecoms/registration">Registrate</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="js/main.js"></script>
</body>

</html>