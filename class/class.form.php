<?php
class Form {

	private $changed = array();

	private $id;
	private $civilite;
	private $nom;
	private $prenom;
	private $date_de_naissance;
	private $email;
	private $adresse;
	private $cp;
	private $ville;
	private $numero;
	private $fichier_joint;
	private $created;
	private $modified;
	private $supprimer;
	
	private $errors;

	private $bdd;

	public function __construct() {
		try
		{
			$this->bdd = new PDO('mysql:host=local.dev;dbname=contact;charset=utf8', 'root', 'vagrant');
		}
		catch (Exception $e)
		{
			die('Erreur : ' .$e->getMessage());
		}
	}

	public function get_id() {
		return $this->id;
	}

	public function set_id($id) {
		if ($this->id != $id) {
			$this->changed['id'] = true;
		}
		$this->id = $id;
	}

	public function get_civilite() {
		return $this->civilite;
	}

	public function set_civilite($civilite) {
		if ($this->civilite != $civilite) {
			$this->changed['civilite'] = true;
		}
		$this->civilite = $civilite;
	}

	public function get_nom() {
		return $this->nom;
	}

	public function set_nom($nom) {
		if ($this->nom != $nom) {
			$this->changed['nom'] = true;
		}
		$this->nom = $nom;
	}

	public function get_prenom() {
		return $this->prenom;
	}

	public function set_prenom($prenom) {
		if ($this->prenom != $prenom) {
			$this->changed['prenom'] = true;
		}
		$this->prenom = $prenom;
	}

	public function get_date_de_naissance() {
		return $this->date_de_naissance;
	}

	public function set_date_de_naissance($date_de_naissance) {
		if ($this->date_de_naissance != $date_de_naissance) {
			$this->changed['date_de_naissance'] = true;
		}
		$this->date_de_naissance = $date_de_naissance;
	}

	public function get_email() {
		return $this->email;
	}

	public function set_email($email) {
		if ($this->email != $email) {
			$this->changed['email'] = true;
		}
		$this->email = $email;
	}

	public function get_adresse() {
		return $this->adresse;
	}

	public function set_adresse($adresse) {
		if ($this->adresse != $adresse) {
			$this->changed['adresse'] = true;
		}
		$this->adresse = $adresse;
	}

	public function get_cp() {
		return $this->cp;
	}

	public function set_cp($cp) {
		if ($this->cp != $cp) {
			$this->changed['cp'] = true;
		}
		$this->cp = $cp;
	}

	public function get_ville() {
		return $this->ville;
	}

	public function set_ville($ville) {
		if ($this->ville != $ville) {
			$this->changed['ville'] = true;
		}
		$this->ville = $ville;
	}

	public function get_numero() {
		return $this->numero;
	}

	public function set_numero($numero) {
		if ($this->numero != $numero) {
			$this->changed['numero'] = true;
		}
		$this->numero = $numero;
	}

	public function get_fichier_joint() {
		return $this->fichier_joint;
	}

	public function set_fichier_joint($fichier_joint) {
		if ($this->fichier_joint != $fichier_joint) {
			$this->changed['fichier_joint'] = true;
		}
		$this->fichier_joint = $fichier_joint;
	}

	public function get_created() {
		return $this->created;
	}

	public function set_created($created) {
		if ($this->created != $created) {
			$this->changed['created'] = true;
		}
		$this->created = $created;
	}

	public function get_modified() {
		return $this->modified;
	}

	public function set_modified($modified) {
		if ($this->modified != $modified) {
			$this->changed['modified'] = true;
		}
		$this->modified = $modified;
	}

	public function get_supprimer() {
		return $this->supprimer;
	}

	public function set_supprimer($supprimer) {
		if ($this->supprimer != $supprimer) {
			$this->changed['supprimer'] = true;
		}
		$this->supprimer = $supprimer;
	}

	public function save() {
		if (!$this->id) {
			$req = $this->bdd->prepare('INSERT INTO contact (civilite, nom, prenom, date_de_naissance, email, adresse, code_postal, ville, numero_telephone, fichier_joint, created, modified, supprimer) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)');
			$req->execute(array($this->civilite, $this->nom, $this->prenom, $this->date_de_naissance, $this->email, $this->adresse, $this->cp, $this->ville, $this->numero, implode($_SESSION['fichier'], ' / '), false));
		}
	}

	public function update() {
		if (count($this->changed) > 0) {
			$sql = "UPDATE contact SET ";
			$listFields = []; $listValues = [];
			foreach($this->changed as $key => $value) {
				$listFields[] = $key.' = ?';
				$listValues[] = $this->$key;
			}
			$sql .= implode($listFields,',');
			$sql .= ", modified = NOW() WHERE id = ".$this->id;
			$req = $this->bdd->prepare($sql);
			$req->execute(array_values($listValues));
		}
	}

	//Form::deleteThisId(15);
	static function deleteThisId($id) {
		$req = $this->bdd->prepare('UPDATE contact SET supprimer = ?, modified = NOW() WHERE id = ?');
		return $req->execute(array(true, $id));
	}

	public function delete() {
		if ($this->id){
			$req = $this->bdd->prepare('UPDATE contact SET supprimer = ?, modified = NOW() WHERE id = ?');
			$req->execute(array(true, $this->id));			
		}
	}

	static function restoreThisId($id) {
		$req = $this->bdd->prepare('UPDATE contact SET supprimer = ?, modified = NOW() WHERE id = ?');
		return $req->execute(array(false, $id));
	}

	public function restore() {
		if ($this->id){
			$req = $this->bdd->prepare('UPDATE contact SET supprimer = ?, modified = NOW() WHERE id = ?');
			$req->execute(array(false, $this->id));
		}
	}

	public function load($id) {
		$req = $this->bdd->prepare('SELECT * FROM contact WHERE id = ?');
		$req->execute(array($id));
		$res = $req->fetchall();
		foreach ($res as $key) {
			$this->id = $key['id'];
			$this->civilite = $key['civilite'];
			$this->nom = $key['nom'];
			$this->prenom = $key['prenom'];
			$this->date_de_naissance = $key['date_de_naissance'];
			$this->email = $key['email'];
			$this->adresse = $key['adresse'];
			$this->cp = $key['code_postal'];
			$this->ville = $key['ville'];
			$this->numero = $key['numero_telephone'];
			$this->fichier_joint = $key['fichier_joint'];
			$this->created = $key['created'];
			$this->modified = $key['modified'];
			$this->supprimer = $key['supprimer'];
		}
	}

	public function get_errors() {
		$this->errors = [];

		if (empty($this->civilite) OR empty($this->nom) OR empty($this->prenom) OR empty($this->date_de_naissance) OR empty($this->email) OR empty($this->adresse) OR empty($this->cp) OR empty($this->ville) OR empty($this->numero)) {
			$this->errors[] = 'Tous les champs requis doivent être remplis';
		}

		if (preg_match('/[^a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s-]+/', $this->nom)) {
			$this->errors[] = 'Le nom est incorrect';
		}

		if (preg_match('/[^a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s-]+/', $this->prenom)) {
			$this->errors[] = 'Le prénom est incorrect';
		}

		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			$this->errors[] = 'L\'email est incorrect';
		}

		if (preg_match('/[^a-zA-Z\d\s\-\,\#\.\+]+/', $this->adresse)) {
			$this->errors[] = 'L\'adresse est incorrect';
		}

		if (preg_match('/[^0-9{5}]/', $this->cp)) {
			$this->errors[] = 'Le code postal est incorrect';
		}

		if (preg_match('/[^a-zA-Z\/\sàáâãäåçèéêëìíîïðòóôõöùúûüýÿ-]+/', $this->ville)) {
			$this->errors[] = 'Le nom de la ville est incorrect';
		}

		if (preg_match('/[^0-9{10}]/', $this->numero)) {
			$this->errors[] = 'Le numéro est incorrect';
		}

		if (count($this->errors)>0) {
			return $this->errors;
		}
		return false;
	}
}