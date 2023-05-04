<?php

// Espace de nom du plugin
namespace plugin_spaa;



/**
 * class-functions
 *
 * Fichier contenant la classe function
 *
 * @link       https://www.arthurbazin.com
 * @since      1.0.0
 *
 * @author     Arthur Bazin
 */



/**
 * Classe de fonctionnalités du plugin
 *
 * Cette classe apporte les fonctions du plugin
 *
 * @since      1.0.0
 * @author     Arthur Bazin
 */
class functionalities {

	/**
	 * Objet contenant la classe parente
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object       $parent_object
	 */
	public $parent_object;

	/**
	 * Nom de l'option qui stocke les données
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string       $option_name
	 */
	public $option_name;

	/**
	 * Temps en seconde de préemption des données stockées (avant appel de l'API)
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      integer       $delai_peremption
	 */
	public $delai_peremption;

	/**
	 * Clé d'api
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string       $api_key
	 */
	public $api_key;

	/**
	 * Code INSEE de la commune
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string       $code_insee
	 */
	public $code_insee;



	/**
	 * Mise en place des fonctionnalités
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct($init_parent_object) {

		// Définition des variables
		$this->parent_object = $init_parent_object;
		$this->option_name = ($this->parent_object->get_plugin_abrv()).'_data';
		$this->delai_peremption = 3; 	// 3 heures

		// Récupération des paramètres du plugin
		$this->api_key = (get_option($this->parent_object->plugin_object_admin->plugin_setting_name))['cle_api'];
		$this->code_insee = (get_option($this->parent_object->plugin_object_admin->plugin_setting_name))['code_insee'];


		// Ajout d'un shortcode dédié
		add_shortcode( 'spaa', array( $this, 'shortcode_function' ) );

	}



	/**
	 * Shortcode d'affichage des données
	 * Les données sont sous cette forme :
	 * [
	 * 	'vigilance' => 'data',
	 * 	'indice' => [
	 * 		'n' => [
	 * 			'global_valeur' => 'Valeur du paramètre',
	 * 			'global_indice' => 'Indice',
	 * 			'global_couleur' => 'Code couleur hexadecimal',
	 * 			'PM2.5' => 'Code HTML contenant les données',
	 * 			'PM10' => 'Code HTML contenant les données',
	 * 			'SO2' => 'Code HTML contenant les données',
	 * 			'O3' => 'Code HTML contenant les données',
	 * 			'NO2' => 'Code HTML contenant les données'
	 * 		],
	 * 		'n+1' => [
	 * 			'global_valeur' => 'Valeur du paramètre',
	 * 			'global_indice' => 'Indice',
	 * 			'global_couleur' => 'Code couleur hexadecimal',
	 * 			'PM2.5' => 'Code HTML contenant les données',
	 * 			'PM10' => 'Code HTML contenant les données',
	 * 			'SO2' => 'Code HTML contenant les données',
	 * 			'O3' => 'Code HTML contenant les données',
	 * 			'NO2' => 'Code HTML contenant les données'
	 * 		]
	 * 	]
	 * ]
	 * Les paramètres du shortcode sont :
	 * 	indicateur
	 * 	echeance
	 * 	parametre
	 *
	 * @since     1.0.0
	 * @param     array     $atts         Attributs
	 * @param     string    $content      Contenu
	 * @param     string    $shortcode    Nom du shortcode
	 * @return    string    La données affichée
	 */
	public function shortcode_function($atts, $content, $shortcode){


		// Récupération des attributs
		$attribut = shortcode_atts(
			array(
				'indicateur' => 'indice',
				'echeance' => 'n',
				'parametre' => 'global_indice'
			), 
			$atts
		);


		// Récupération des données 
		$data = $this->get_data(
			$this->option_name,
			$this->delai_peremption,
			$this->api_key,
			$this->code_insee
		);


		// Utilisation d'un try pour éviter les erreurs PHP
		try {

			// Récupération de la donnée
			if ( 
				gettype($atts) == 'array'
				and (
					in_array('debug', $atts) 
					or array_key_exists('debug', $atts)
				)
			){
				$data_return = $data;
			}
			else if ( $attribut['indicateur'] == 'indice' ){
				$data_return = $data[$attribut['indicateur']][$attribut['echeance']][$attribut['parametre']];
			}
			else if ( $attribut['indicateur'] == 'vigilance' ){
				$data_return = $data[$attribut['indicateur']][$attribut['echeance']][$attribut['parametre']];
			}
			else {
				throw new \Exception('Mauvais paramètre');
			}

		} 

		catch (\Throwable $th) {

			$data_return = 'Les paramètres indiqués sont incorrects';

		}

		finally {

			// Formatage éventuel de la donnée
			if (is_array($data_return)) {
				return json_encode($data_return);
			}
			else {
				return $data_return;
			}

		}

	}



	/**
	 * Récupération des données à afficher
	 *
	 * @since     1.0.0
	 * @param     string    $option_name          Nom de l'option qui stocke les données
	 * @param     string    $delai_peremption     Temps en seconde de préemption des données stockées (avant appel de l'API)
	 * @param     string    $api_key              Clé d'api
	 * @param     string    $code_insee           Code INSEE de la commune
	 * @return    array     Un tableau de données
	 */
	public function get_data($option_name, $delai_peremption, $api_key, $code_insee){

		// Récupération des données stockées
		$data = get_option($option_name);

		$current_timestamp = new \DateTime('now', new \DateTimeZone('UTC'));


		// Calcul de l'interval depuis la dernière récupération des données
		if( $data['timestamp_data'] ){

			$timestamp_interval = $current_timestamp->getTimestamp() - $data['timestamp_data'];

		}
		else {

			$timestamp_interval = $delai_peremption;

		}


		// Si les données sont périmées
		if( $timestamp_interval >= $delai_peremption ) {

			// Récupération données d'API
			$atmo_data = $this->get_api_data($api_key, $code_insee);

			// Mise en forme des données
			$data['vigilance'] = $this->format_data_vigilance($atmo_data['vigilance']);
			$data['indice'] = $this->format_data_indice($atmo_data['indice']);

			// Assignation d'un timestamp
			$data['timestamp_data'] = $current_timestamp->getTimestamp();

			// Sauvegarde des données	
			update_option($option_name, $data);

		}

		return $data;

	}



	/**
	 * Récupération des données issues de l'API Atmo AuRA
	 *
	 * @since     1.0.0
	 * @param     string    $api_key        Clé d'api
	 * @param     string    $code_insee     Code INSEE de la commune
	 * @return    array     Un tableau de données par api appelée
	 */
	public function get_api_data($api_key, $code_insee){

		// APIs
		$api = [
			'indice' => [
				'url' => "http://api.atmo-aura.fr/api/v1/communes/{$code_insee}/indices/atmo?api_token={$api_key}&date_debut_echeance=now",
				'parameter' => [
					'method' => 'GET',
					'timeout' => 30
				]
			],
			'vigilance' => [
				'url' => "http://api.atmo-aura.fr/api/v1/communes/{$code_insee}/vigilances?api_token={$api_key}&date=now",
				'parameter' => [
					'method' => 'GET',
					'timeout' => 30
				]
			],
		];

		// Récupération des données
		$response = array();
		$data = array();
		
		foreach ($api as $key => $value){
			$response[$key] = wp_remote_get($value['url'], $value['parameter']);

			// Traitement de la réponse
			if(
				!is_wp_error($response[$key]) 
				&& (
					$response[$key]['response']['code'] == 200 
					|| $response[$key]['response']['code'] == 201
				)
			) {
				$data[$key] = array();

				$data[$key] = json_decode($response[$key]['body'], TRUE);

			}
			else {
				$data[$key] = ($response[$key]);
			}
		}
		
		return $data;
	}



	/**
	 * Formatage des données de vigilance
	 *
	 * @since     1.0.0
	 * @param     array     $data     Tableau de données
	 * @return    string    Un contenu HTML
	 */
	public function format_data_vigilance($data){

		$data_return = $data;


		if ($data['data']['vigilances'] == null){

			$data_return = '<p>Aucune vigilance particulière</p>';

		}
		else{

			$data_return = '<ul>';

			foreach ($data['data']['vigilances'] as $key){
				$data_return .= '<li class="vigi_niveau_'.$data['data']['vigilances'][$key]['niveau'].'">';
				$data_return .= '<span class="vigi_title">'.$data['data']['vigilances'][$key]['nom_procedure'].'</span><br/>';
				$data_return .= '<span class="vigi_date">Du '.$data['data']['vigilances'][$key]['date_debut'].' au '.$data['data']['vigilances'][$key]['date_fin'].'</span><br/>';
				$data_return .= '<span class="vigi_zone">Zone '.$data['data']['vigilances'][$key]['zone'].'</span><br/>';
				$data_return .= '<span class="vigi_polluant">Polluant '.$data['data']['vigilances'][$key]['polluant'].'</span><br/>';
				$data_return .= '<span class="vigi_niveau">Niveau '.$data['data']['vigilances'][$key]['niveau'].'</span><br/>';
				$data_return .= '<span class="vigi_commentaire">'.$data['data']['vigilances'][$key]['commentaire'].'</span>';
				$data_return .= '</li>';

			}

			$data_return .= '</ul>';
		}

		return $data_return;

	}



	/**
	 * Formatage des données d'indice
	 *
	 * @since     1.0.0
	 * @param     array     $data     Tableau de données
	 * @return    array     Un tableau de données
	 */
	public function format_data_indice($data){

		$data_return['debug'] = $data;


		// Tri des données dans l'ordre d'échéance
		// La première échéance est toujours aujourd'hui (car l'API est filtrée sur now
		usort(
			$data['data'], 
			function($a, $b) {
				return $a['echeance'] <=> $b['echeance'];
			}
		);


		foreach ($data['data'] as $key => $elem){

			// Définition de l'indice
			if ($key == 0){
				$indice = 'n';
			}
			else{
				$indice = 'n+'.$key;
			}


			// Valeurs globales
			$data_return[$indice]['global_valeur'] = $elem['indice'];
			$data_return[$indice]['global_indice'] = $elem['qualificatif'];
			$data_return[$indice]['global_couleur'] = $elem['couleur_html'];


			// Indice par polluant
			foreach ($elem['sous_indices'] as $ss_indice){
				$polluant = $ss_indice['polluant_nom'];
				switch ($polluant):
					case 'PM2.5':
						$polluant_fr = "Particules fines, diamètre < 2,5 µm";
						break;
					case 'PM10':
						$polluant_fr = "Particules fines, diamètre < 10 µm";
						break;
					case 'SO2':
						$polluant_fr = "Dioxyde de soufre";
						break;
					case 'O3':
						$polluant_fr = "Ozone";
						break;
					case 'NO2':
						$polluant_fr = "Dioxyde d'azote";
						break;
					default:
						$polluant_fr = "";
				endswitch;

				$concentration = $ss_indice['concentration'];
				$indice_polluant = $ss_indice['indice'];

				$data_return[$indice][$polluant]  = '<span class="polluant indice_'.$indice_polluant.'">';
				$data_return[$indice][$polluant] .= '<span class="pollu_title">'.$polluant_fr.'</span>';
				$data_return[$indice][$polluant] .= ' (<span class="pollu_molecule">'.$polluant.'</span>)';
				$data_return[$indice][$polluant] .= '<span class="pollu_info indice_'.$indice_polluant.'">';
				$data_return[$indice][$polluant] .= '	<span class="pollu_indice">'.$indice_polluant.'</span>';
				$data_return[$indice][$polluant] .= '	<span class="pollu_concentration">'.$concentration.'</span>';
				$data_return[$indice][$polluant] .= '</span>';
				$data_return[$indice][$polluant] .= '</span>';
			}
		}


		return $data_return;

	}




































}