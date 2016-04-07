<?php

class Upload {

	private $tab_files;

	public function __construct() {
	}

	public function test_file() {
		if (filesize($_FILES['file']['tmp_name']) > 6000) {

			$message = [
				'error' => true,
				'message' => 'La taille de '.$_FILES['file']['name'].' est trop grande'
			];

			die(json_encode($message));
		}

		$extensions = array('.png', '.gif', '.jpg', '.jpeg');
		$extension = strrchr($_FILES['file']['name'], '.');
		if (!in_array($extension, $extensions)) {

			$message = [
				'error' => true,
				'message' => 'Le format de '.$_FILES['file']['name'].' n\'est pas valide'
			];

			die(json_encode($message));
		}
	}

	public function upload() {
		$carac = array('Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
		'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
		'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
		'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
		'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
		'Œ' => 'oe', 'œ' => 'oe',
		'$' => 's',
		' ' => '-', '\'' => '-', ',' => '-');
		$new_name = strtr($_FILES['file']['name'], $carac);
		$new_name = strtolower(trim($new_name));
		$new_name = date("Ymd").$new_name;

		if (file_exists('fichier_joint/'.$new_name)) {

			$message = [
				'error' => true,
				'message' => ''.$_FILES['file']['name'].' existe déjà'
			];

			die(json_encode($message));
		}

		move_uploaded_file($_FILES['file']['tmp_name'],'fichier_joint/'.$new_name);
		$tab_files = [];
		$tab_files[] = $new_name;
		foreach ($tab_files as $k) {
			array_push($_SESSION['fichier'], $k);
		}
		$old = 'fichier_joint/'.$_FILES['file']['name'];
		$new = 'fichier_joint/'.$new_name;
		$html = [
			'error' => false,
			'html' => '<div class="file"><img src="'.$new.'"/> '.basename($old).' <div class="actions"><a href="'.basename($new).'" class="del">Supprimer</a></div></div>'
		];
		die(json_encode($html));
	}
}