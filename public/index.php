<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Paris');

require '../class/class.form.php';
require '../class/class.upload.php';

if (!isset($_SESSION['fichier'])) $_SESSION['fichier'] = array();

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
	unlink('fichier_joint/'.$_GET['file']);
	die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	if (is_array($_FILES['file'])) {
		$upload = new Upload();

		$upload->test_file();

		$upload->upload();
	}

	if (empty($_POST['civilite']) OR empty($_POST['nom']) OR empty($_POST['prenom']) OR empty($_POST['date_de_naissance']) OR empty($_POST['email']) OR empty($_POST['adresse']) OR empty($_POST['cp']) OR empty($_POST['ville']) OR empty($_POST['numero'])) {
		http_response_code(400);
		echo "Veuillez remplir tous les champs requis";
		die;
	}
	else {
		$form = new Form();

		$form->set_civilite($_POST['civilite']);
		$form->set_nom(strip_tags(trim($_POST['nom'])));
		$form->set_prenom(strip_tags(trim($_POST['prenom'])));
		$form->set_date_de_naissance($_POST["date_de_naissance"]);
		$form->set_email(filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL));
		$form->set_adresse(trim($_POST['adresse']));
		$form->set_cp(trim($_POST['cp']));
		$form->set_ville(trim($_POST['ville']));
		$form->set_numero(trim($_POST['numero']));

		$res = $form->get_errors();

		if (is_array($res)) {
			foreach ($res as $key) {
				echo $key;
				unset($_SESSION['fichier']);
				die();
			}
		}
		else {
			http_response_code(200);
			echo "Merci, vos informations ont bien été prises en compte";
			$form->save();
			unset($_SESSION['fichier']);
		}
	}
}
?>

<!DOCTYPE html>
<html ng-app>
<head>
	<title>formulaire</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
</head>

<body>
	<div id="form-messages"></div>
	<form name="myForm" id="ajax-contact" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
		<table>
			<tr>
				<td align="center" colspan="2">* Champ requis</td>
			</tr>

			<tr>
				<div>
					<td><label for="civilite">Civilité * :</label></td>
					<td align="center"><input type="radio" name="civilite" value="Mr" required /> Monsieur
					<input type="radio" name="civilite" value="Ms" /> Madame
				</div>
			</tr>

			<tr>
				<div>
					<td><label for="nom">Nom * :</label></td>
					<td align="center"><input type="text" class="form-control" name="nom" id="nom" ng-model="user.nom" pattern="[a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s-]+" placeholder="Nom" required /></td>
					<td><span ng-show="myForm.nom.$invalid && myForm.nom.$touched">Le nom n'est pas valide</span></td>
				</div>
			</tr>
			
			<tr>
				<div>
					<td><label for="prenom">Prénom * : </label></td>
					<td align="center"><input type="text" class="form-control" name="prenom" id="prenom" ng-model="user.prenom" pattern="[a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s-]+" placeholder="Prénom" required /></td>
					<td><span ng-show="myForm.prenom.$invalid && myForm.prenom.$touched">Le prénom n'est pas valide</span></td>
				</div>
			</tr>
			
			<tr>
				<div>
					<td><label for="date_de_naissance">Date de naissance * :</label></td>
					<td align="center"><input type="date" class="form-control" name="date_de_naissance" id="date_de_naissance" ng-model="user.date_de_naissance" placeholder="Date de naissance" required /></td>
					<td><span ng-show="myForm.date_de_naissance.$invalid && myForm.date_de_naissance.$touched">La date de naissance doit être de la forme jj/mm/aaaa ex : 14/12/1991</span></td>
				</div>
			</tr>
			
			<tr>
				<div>
					<td><label for="email">Email * :</label></td>
					<td align="center"><input type="email" class="form-control" name="email" id="email" ng-model="user.email" pattern="^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,4}$" placeholder="Email" required /></td>
					<td><span ng-show="myForm.email.$invalid && myForm.email.$touched">L'email n'est pas valide</span></td>
				</div>
			</tr>
			
			<tr>
				<div>
					<td><label for="adresse">Adresse * :</label></td>
					<td align="center"><input type="text" class="form-control" name="adresse" id="adresse" ng-model="user.adresse" pattern="[a-zA-Z\d\s\-\,\#\.\+]+" placeholder="Adresse" required /></td>
					<td><span ng-show="myForm.adresse.$invalid && myForm.adresse.$touched">L'adresse n'est pas valide</span></td>
				</div>
			</tr>
			
			<tr>
				<div>
					<td><label for="cp">Code postal * :</label></td>
					<td align="center"><input type="text" class="form-control" name="cp" id="cp" ng-model="user.cp" pattern="[0-9]{5}" placeholder="Code postal" required /></td>
					<td><span ng-show="myForm.cp.$touched && myForm.cp.$invalid">Le code postal n'est pas valide</span>
				</div>
			</tr>
			
			<tr>
				<div>
					<td><label for="ville">Ville * :</label></td>
					<td align="center"><input type="text" class="form-control" name="ville" id="ville" ng-model="user.ville" pattern="[a-zA-Z\/\sàáâãäåçèéêëìíîïðòóôõöùúûüýÿ-]+" placeholder="Ville" required /></td>
					<td><span ng-show="myForm.ville.$invalid && myForm.ville.$touched">Le nom de la ville n'est pas valide</span></td>
				</div>
			</tr>

			<tr>
				<div>
					<td><label for="numero">Numéro de téléphone * :</label></td>
					<td align="center"><input type="text" class="form-control" name="numero" id="numero" ng-model="user.numero" pattern="[0-9]{10}" placeholder="Numéro de téléphone" required /></td>
					<td><span ng-show="myForm.numero.$invalid && myForm.numero.$touched">Le numéro doit contenir 10 chiffres ex : 0123456789</span></td>
				</div>
			</tr>

			<tr>
				<div id="plupload">
					<td colspan="2" align="center">
						<div id="droparea">
							<p>Déposer vos fichiers ici</p>
							<span class="or">ou</span>
							<a href="#" id="browse" name="file">Parcourir</a>
						</div>
					<td>
				</div>
			</tr>
			
			<tr>
				<td colspan="2"><div id="filelist">
					<?php foreach(glob('fichier_joint/*.*') as $v): ?>
						<div class="file">
							<img src="<?php echo $v; ?>"/>
							<?php echo basename($v); ?>
							<div class="actions">
								<a href="<?php echo basename($v); ?>" class="del">Supprimer</a>
							</div>
						</div>
					<?php endforeach; ?>
				</div></td>
			</tr>

			<tr>
				<td align="center" colspan="2"><input class="btn btn-default" type="submit" id="upload" name="submit" value="Envoyer" /></td>
			</tr>
		</table>
	</form>
</body>

<footer>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/app.js"></script>
	<script type="text/javascript" src="js/plupload/plupload.js"></script>
	<script type="text/javascript" src="js/plupload/plupload.html5.js"></script>
	<script type="text/javascript" src="js/plupload/plupload.flash.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/webshim/1.14.5/polyfiller.js"></script>
	<script type="text/javascript" src="js/js.js"></script>
</footer>
</html>