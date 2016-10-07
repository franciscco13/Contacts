<?php if(!isset($_SESSION))
        session_start();
    if(isset($_SESSION['perfil'])){
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html ng-app="myApp">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta charset="utf-8">
    <title>login</title>
   <script src = "lib/jquery.min.js"></script> 
    <script src = "lib/angular.min.js"></script>    
    <script src = "lib/angular-sanitize.min.js"></script>
    <script src = "lib/angular-filters.min.js"></script>
    <script src = "lib/materialize/bin/materialize.min.js"></script> 
    <link rel="stylesheet" href="http://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="css/materialize.css" />
    <link rel="stylesheet" href="css/login.css" />
</head>

<body>
    <section ng-controller="controller-login-register" class="form-container">
        <form class="card-panel hoverable login-form" action="POST"> 
            <div class="input-field">
                <input type="text" id="email" ng-model="email" />
                <label for="email">Email</label>
            </div>
            <div class="input-field">
                <input type="password" id="password" ng-model="pass" />
                <label for="password">Contraseña</label>
            </div>
            </br>
            <a class="waves-effect waves-deep-purple btn-large right" ng-click="login()">Iniciar sesion</a>         
        </form> 
    </section>
    <script>
    var app = angular.module("myApp", ['ngSanitize']);
    function isValid(s) {
        return !(s == undefined || s == "");
    }

    app.controller("controller-login-register", function($scope, $http, $window, $timeout) { 
     
        $scope.login = function() {
            var email = $scope.email;
            var pass = $scope.pass;

            if (!isValid(email) || !isValid(pass)) {
                alert("Ingresa un email y una contraseña");
                return;
            }

            if (!/.+@.+\..+/.test(email)) {
                alert("Ingresa un email correcto");
                return;
            } 

            pass = pass.trim();
            $http({
                method: 'POST',
                /*
                Colocar la url del archivo PHP donde se recibirán los campos 
                'email' y 'pass' mediante el protocolo POST y se evaluará la existencia 
                estos campos en la base de datos dentro de las tablas 'admin' o 'nutriologo'.

                Devolver un "echo [valor]" del lado del servidor, en donde
                [valor] está definido de acuerdo a las condiciones:
                    
                -1: Los valores no se encontraron en ninguna tabla
                 0: EL email existe pero la contraseña no es correcta
                 1: El email y la contraseña existen solo dentro de la tabla 'Nutriologo'
                 2: El email y la contraseña existen solo dentro de la tabla 'admin'
                */
                url: 'php/do.php?do=login',
                data: $.param({
                    email: email,
                    password: pass
                }),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }).success(function(data, status, headers, config) {
                console.log(data);
                switch (data) {  
                    case "0":
                        alert("Error al iniciar sesión");
                        break;
                    case "1":
                        $window.location.href = "index.php";
                        break; 
                }
            }).error(function(data, status, headers, config) {
                alert("Ha ocurrido un error, inténtalo más tarde.");
            });
        };  
    });
    </script>
</body>

</html>
