<?php if(!isset($_SESSION))
        session_start();
    if(!isset($_SESSION['perfil'])){
        header("Location: login.php");
    }
?>

<!DOCTYPE html>
<html ng-app = "myApp" ng-cloak>
<head>  
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name = "viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">	
	<meta charset="utf-8">	

	<title>administrador</title> 	
	<link rel="stylesheet" href="css/materialize.css">	
	<link rel="stylesheet" href="fonts/flaticon/flaticon.css">
	<link rel="stylesheet" href="fonts/walfork/walfork.css">
	<link rel="stylesheet" href="css/index.css">

	<script src = "lib/jquery.min.js"></script> 
	<script src = "lib/angular.min.js"></script>	
 	<script src = "lib/angular-sanitize.min.js"></script>
 	<script src = "lib/angular-filters.min.js"></script>
 	<script src = "lib/materialize/bin/materialize.min.js"></script>
	<script src = "https://code.angularjs.org/1.0.8/i18n/angular-locale_es-mx.js"></script> 
	
</head>
<body ng-controller = "contact-main-page"> 

	<section class="navbar"> 
		<div class="admin-section" ng-click = "logout()">
			<div class="name">{{admin.name}}</div>
			<div class="img">{{admin.name[0]}}</div>
		</div>
	</section>


	<div class="content">

		<section class = "tabcontainer">
			<div class="default-tab"><i class="flaticon-home"></i>Dashboard</div>			
			<!-- <div class="default-tab"><i class="flaticon-statistic2"></i>Estadísticas</div>
			<div class="default-tab selected"><i class="flaticon-draft"></i>Posts</div>
			<div class="default-tab"><i class="flaticon-artists"></i>Artistas</div>
			<div class="default-tab"><i class="flaticon-comment"></i>Comentarios</div>
			<div class="default-tab"><i class="flaticon-settings"></i>Configuración</div> -->
		</section>


		<section class = "workarea">
			<table>
				<thead>
			      	<tr>				       
				        <th id = "th-apP"     ng-click="sort('apellidoPat', $event)" class = "selected-up">Ap. Paterno</th>
				        <th id = "th-apM"     ng-click="sort('apellidoMat', $event)">Ap. Materno</th>
				        <th id = "th-nombre"  ng-click="sort('name', $event)">Nombre</th>
				        <th id = "th-job"     ng-click="sort('job', $event)">Profesión</th>
				        <th id = "th-tel"     ng-click="sort('tel', $event)">Telefono</th>
			      	</tr>
			    </thead>
			    <tbody ng-init = "order = 'artistname'">
					<tr ng-repeat = "x in users | orderBy:category" user-id = "{{x.id}}"> 	
						<td class = "apP"><input disabled type="text" ng-model = "x.apellidoPat"></td>
						<td class = "apM"><input disabled type="text" ng-model = "x.apellidoMat"></td>
						<td class = "name"><input disabled type="text" ng-model = "x.name"></td> 
						<td class = "job"><input disabled type="text" ng-model = "x.job"></td> 
						<td class = "tel"><input disabled type="text" ng-model = "x.tel"></td> 
						<td class = "modify" ng-click="modify($event, x)"><i class = "flaticon-edit"></i></td>
						<td class = "delete" ng-click="delete($event, x)"><i class = "flaticon-delete"></i></td>
						<td class = "unsave" ng-click = "unsave()"><i class = "flaticon-close"></i></td>		
						<td class = "save" ng-click = "save()"><i class = "flaticon-checked"></i></td>
					</tr>
				</tbody>
			</table>
		</section>
	</div>
 

	<div id="add" class = "state__btn">
		<i ng-click = "add()" class = "flaticon-add"></i>  
		<h1>Nuevo registro</h1>

		<div class="input-field">
			<input id="new_apellidoPat" type="text" ng-model = "new.apellidoPat">
			<label for="new_apellidoPat">Ap. Paterno</label>
		</div>
		<div class="input-field">
			<input id="new_apellidoMat" type="text" ng-model = "new.apellidoMat">
			<label for="new_apellidoMat">Ap. Materno</label>
		</div>
		<div class="input-field">
			<input id="new_name" type="text" ng-model = "new.name">
			<label for="new_name">Nombre</label>
		</div>	
		<div class="input-field">
		    <select>  
		      	<option value="diets">Nutriologo</option>
		      	<option value="dentists">Dentista</option> 
		    </select>
		    <label>Profesión</label>
		</div>
		<div class="input-field">
			<input id="new_tel" type="number" ng-model = "new.tel">
			<label for="new_tel">Teléfono</label>
		</div>

		<div class = "new_btn" ng-click = "unsave()"><i class = "flaticon-close"></i></div>		
		<div class = "new_btn" ng-click = "save()"><i class = "flaticon-checked"></i></div>
<!-- 
		<div class="input-field col s12">
          <input id="email" type="email" class="validate">
          <label for="email">Email</label>
        </div> -->
	</div> 
	
	<script type="text/javascript">	
		var app = angular.module("myApp", ['ngSanitize']);
		app.controller("contact-main-page", function($scope, $http, $window){
 			
			var openRow = false;			

			$http.get("php/do.php", {params: {"do":"getSession"}}).then(function(response){
				console.log(response);
				$scope.admin = {};
				$scope.admin.name = response.data.nombre;
				$scope.admin.id   = response.data.id;
			});

			$http.get("php/do.php", {params: {"do":"getAllData"}}).then(function(response){  
				$scope.users = response.data; 
			});

			$scope.category = 'apellidoPat';
			$scope.sort = function(value, $event){
					var id = $event.target.id;
				$('thead th').removeClass('selected-up').removeClass('selected-down');
				if($scope.category == value){
					$scope.category = '-'+value;		
					$('#' + id).addClass('selected-down');	}
				else{
					$scope.category = value;
					$('#' + id).addClass('selected-up');}
			};  

			$scope.delete = function($event, x){
				if(openRow){
					alert("Guarda o descarta los cambios de la actual tupla");
					return;
				}

				if(confirm("Seguro que deseas eliminar el registro?")){ 
					$http.get("php/do.php", {
						params: {
							"do":"deleteData",
							"db_id": x.id
						}
					}).then(function(response){  
						var row = $($event.currentTarget).parent(); 
						row.hide();
					});
			    }
			    return;			
			};

 
			$scope.modify = function($event, x){
				if(openRow){
					alert("Guarda o descarta los cambios de la actual tupla");
					return;
				}
				openRow = true;
 			 	var row = $($event.currentTarget).parent(); 
				var array = angular.copy(x); 

			 	row.find("td.modify").hide();
				row.find("td.delete").hide();
				row.find("td.save").css("display", "inline-block");
				row.find("td.unsave").css("display", "inline-block");
				row.find("input").prop("disabled", false);
				row.find(".job input").prop("disabled", true);
			 	row.addClass("fixed-row"); 
 
			 	$scope.unsave = function($event){  

			 		var apP_input = row.find(".apP input");
			 		var apM_input = row.find(".apM input");
			 		var name_input = row.find(".name input");
			 		var job_input = row.find(".job input");
			 		var tel_input = row.find(".tel input");

			 		apP_input.val(array.apellidoPat);
			 		apM_input.val(array.apellidoMat);
			 		name_input.val(array.name);
			 		job_input.val(array.job);
			 		tel_input.val(array.tel);

			 		apP_input.trigger('input');
			 		apM_input.trigger('input');
			 		name_input.trigger('input');
			 		job_input.trigger('input');
			 		tel_input.trigger('input');

			 		$scope.restoreRowFormat();
			 		row.addClass("last-modified"); 
			 		openRow = false;
				}; 

				$scope.save = function(){
					$http.get("php/do.php", {
						params: {
							"do":"modifyData",
							"db_id": x.id,
							"apP": x.apellidoPat,
							"apM": x.apellidoMat,
							"name": x.name,
							"tel": x.tel
						}
					}).then(function(response){  
						console.log(response);
					});
					$scope.restoreRowFormat();
			 		row.addClass("last-modified"); 
			 		openRow = false;
				}
			};

			$scope.add = function(){ 
				openRow = true;
				$("#add").removeClass("state__btn").addClass("state__form");

				$scope.save = function(){
					$scope.new.job = $("select").val();
					$http.get("php/do.php", {
						params: {
							"do":"addData", 
							"apP": $scope.new.apellidoPat,
							"apM": $scope.new.apellidoMat,
							"name": $scope.new.name,
							"tel": $scope.new.tel,
							"job": $scope.new.job
						}
					}).then(function(response){ 

						openRow = false;
						$("#add").removeClass("state__form").addClass("state__btn"); 
						$scope.users = $scope.users.concat([
							{
								apellidoMat : angular.copy($scope.new.apellidoMat),
								apellidoPat: angular.copy($scope.new.apellidoPat),
								name: angular.copy($scope.new.name),
								id: $scope.new.job+"::"+response.data,
								job: (angular.copy($scope.new.job) == "diets")?"Nutriologo":"Dentista",
								tel: angular.copy($scope.new.tel)
							}
						]);

						// $scope.users
						$scope.new.apellidoPat = "";
						$scope.new.apellidoMat = "";
						$scope.new.name = "";
						$scope.new.tel = "";	
					});
				};

				$scope.unsave = function(){ 
					openRow = false;
					$("#add").removeClass("state__form").addClass("state__btn");
				};
			};

			$scope.restoreRowFormat = function(){
				$("tr").removeClass("fixed-row").removeClass("last-modified");
				$("td.modify").show();
				$("td.delete").show();
				$("td.save").hide();
				$("td.unsave").hide();
				$("table input").prop("disabled", true); 
			};

			$scope.logout = function (){
				$http.get("php/do.php", {params: {"do":"closeSession"}}).then(function(response){
					console.log("closing session...");
					$window.location.href = "login.php";
				});
			};

		}); 
		$(function(){
			$('select').material_select();
			$('thead').pushpin({ top: $('thead').offset().top });
		});
	</script>
</body>
</html>
