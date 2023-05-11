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
	 * Données de définition des indices
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string       $code_insee
	 */
	public $data_definition;



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


		// Récupération des paramètres du plugin
		$this->api_key = 
			isset(
				(get_option($this->parent_object->plugin_object_admin->plugin_setting_name))['cle_api']
			) ?
			(get_option($this->parent_object->plugin_object_admin->plugin_setting_name))['cle_api'] :
			''
		;
		$this->code_insee = 
			isset(
				(get_option($this->parent_object->plugin_object_admin->plugin_setting_name))['code_insee']
			) ?
			(get_option($this->parent_object->plugin_object_admin->plugin_setting_name))['code_insee'] :
			''
		;
		$this->delai_peremption = 
			isset(
				(get_option($this->parent_object->plugin_object_admin->plugin_setting_name))['delai_peremption']
			) ?
			(get_option($this->parent_object->plugin_object_admin->plugin_setting_name))['delai_peremption'] :
			( 3 * 3600 )
		; 	// 3 heures par défaut


		// Ajout d'un shortcode dédié
		add_shortcode( 'spaa', array( $this, 'shortcode_function' ) );

	}



	/**
	 * Shortcode d'affichage des données
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
				'polluant' => 'global',
				'parametre' => 'widget',
				'texte' => 'Données de ma commune sur le site de l\'observatoire de la qualité de l\'air en Auvergne-Rhône-Alpes'
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
				$data_return = $data['indice'][$attribut['echeance']][$attribut['polluant']][$attribut['parametre']];
			}
			else if ( $attribut['indicateur'] == 'vigilance' ){
				$data_return = $data['vigilance'];
			}
			else if ( $attribut['indicateur'] == 'recommandation' ){
				$data_return = $data['indice'][$attribut['echeance']]['recommandation'];
			}
			else if ( $attribut['indicateur'] == 'horodatage' ){

				$date = new \DateTime();
				$date->setTimestamp($data['timestamp_data']);
				$date->setTimezone(new \DateTimeZone(wp_timezone_string()));

				$data_return = $date->format("Y-m-d H:i:s");
			}
			else if ( $attribut['indicateur'] == 'lien' ){
				$data_return = '<a href="https://www.atmo-auvergnerhonealpes.fr/air-commune/_/'.$this->code_insee.'/indice-atmo" target="_blank">'.$attribut['texte'].'</a>';
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
			$this->data_definition = $this->format_data_definition($atmo_data['definition']);
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
			'definition' => [
				'url' => "http://api.atmo-aura.fr/api/v1/indices/definitions/definitions?api_token={$api_key}",
				'parameter' => [
					'method' => 'GET',
					'timeout' => 30
				]
			],
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
			]
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
	 * Formatage des données de définition
	 *
	 * @since     1.0.0
	 * @param     array     $data     Tableau de données
	 * @return    array     Un tableau de données
	 */
	public function format_data_definition($data){

		$data_return['debug'] = $data;


		foreach ($data['data'] as $elem){

			// Récupération de l'indice
			$indice = $elem['indice'];

			// Retrait de la clé
			unset($elem['indice']);

			// Récupération des données
			$data_return[$indice] = $elem;


			// Recommandations
			$data_return[$indice]['recommandation'] = '<div class="spaa recommandations">';

			foreach ($elem['recommandations'] as $reco){

				$data_return[$indice]['recommandation'] .= 
					'<div class="recommandation">'.
					'  <div class="reco_img">'.
					'    <img src="'.$reco['picto_url'].'">'.
					'  </div>'.
					'  <div class="reco_info">'.
					'    <span class="reco_title">'.
					       $reco['categorie'].
					'    </span>'.
					'    <span class="reco_texte">'.
					       $reco['texte'].
					'    </span>'.
					'  </div>'.
					'</div>'
				;

			}

			$data_return[$indice]['recommandation'] .= '</div>';
		}


		return $data_return;

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

			$data_return = '<ul class="spaa vigilance">';

			foreach ($data['data']['vigilances'] as $key){
				$data_return .= 
					'<li class="vigi_niveau_'.$data['data']['vigilances'][$key]['niveau'].'">'.
					'<span class="vigi_title">'.$data['data']['vigilances'][$key]['nom_procedure'].'</span><br/>'.
					'<span class="vigi_date">Du '.$data['data']['vigilances'][$key]['date_debut'].' au '.$data['data']['vigilances'][$key]['date_fin'].'</span><br/>'.
					'<span class="vigi_zone">Zone '.$data['data']['vigilances'][$key]['zone'].'</span><br/>'.
					'<span class="vigi_polluant">Polluant '.$data['data']['vigilances'][$key]['polluant'].'</span><br/>'.
					'<span class="vigi_niveau">Niveau '.$data['data']['vigilances'][$key]['niveau'].'</span><br/>'.
					'<span class="vigi_commentaire">'.$data['data']['vigilances'][$key]['commentaire'].'</span>'.
					'</li>'
				;

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
				$echeance = 'n';
			}
			else{
				$echeance = 'n+'.$key;
			}


			// Valeurs globales
			$data_return[$echeance]['global']['nom'] = 'Indice global';
			$data_return[$echeance]['global']['abbreviation'] = null;
			$data_return[$echeance]['global']['indice num'] = $elem['indice'];
			$data_return[$echeance]['global']['indice txt'] = $this->data_definition[$elem['indice']]['qualificatif'];
			$data_return[$echeance]['global']['concentration'] = null;
			$data_return[$echeance]['global']['image'] = '<img class="picto_indice" src="'.$this->data_definition[$elem['indice']]['picto_url'].'">';
			$data_return[$echeance]['global']['gauge'] = $this->format_data_indice_gauge($data_return[$echeance]['global']);
			$data_return[$echeance]['global']['widget'] = $this->format_data_indice_widget($data_return[$echeance]['global']);

			$data_return[$echeance]['recommandation'] = $this->data_definition[$elem['indice']]['recommandation'];


			// Sous-indice par polluant
			foreach ($elem['sous_indices'] as $ss_indice){
				$polluant = $ss_indice['polluant_nom'];
				switch ($polluant):
					case 'PM2.5':
						$polluant_fr = "Particules fines, ⌀ < 2,5 µm";
						break;
					case 'PM10':
						$polluant_fr = "Particules fines, ⌀ < 10 µm";
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

				$data_return[$echeance][$polluant]['nom'] = $polluant_fr;
				$data_return[$echeance][$polluant]['abbreviation'] = $polluant;
				$data_return[$echeance][$polluant]['indice num'] = $ss_indice['indice'];
				$data_return[$echeance][$polluant]['indice txt'] = $this->data_definition[$ss_indice['indice']]['qualificatif'];
				$data_return[$echeance][$polluant]['concentration'] = $ss_indice['concentration'];
				$data_return[$echeance][$polluant]['image'] = '<img class="picto_indice" src="'.$this->data_definition[$ss_indice['indice']]['picto_url'].'">';
				$data_return[$echeance][$polluant]['gauge'] = $this->format_data_indice_gauge($data_return[$echeance][$polluant]);
				$data_return[$echeance][$polluant]['widget'] = $this->format_data_indice_widget($data_return[$echeance][$polluant]);

			}
		}


		return $data_return;

	}



	/**
	 * Formatage des données d'indice sous forme d'une gauge
	 *
	 * @since     1.0.0
	 * @param     array     $data     Tableau de données
	 * @return    string    Un code HTML représentant les données sous forme de gauge
	 */
	public function format_data_indice_gauge($data){
		$data_return = 
			"<div class=\"spaa indice_gauge\">
				<div class=\"gg_polluant\">".str_replace(", ", "<br>", $data['nom'])."</div>
				<div class=\"conteneur_gauge\">
					<div class=\"gauge\">
						<div class=\"cadran\">
							<div class=\"graduation_item\"></div>
							<div class=\"graduation_item\"></div>
							<div class=\"graduation_item\"></div>
							<div class=\"graduation_item\"></div>
							<div class=\"graduation_item\"></div>
							<div class=\"graduation_item\"></div>
							<div class=\"cadran_centre_bord\">
							</div>
							<div class=\"needle\" style=\"transform: rotate(calc( -15deg + ( {$data['indice num']} * 30deg ) ))\"></div>
							<div class=\"cadran_centre\">
							</div>
						</div>
						{$data['image']}
					</div>
				</div>
				<div class=\"gg_indice\">{$data['indice txt']}</div>
				<div class=\"gg_concentration\">
				". (isset( $data['concentration'] ) ? "({$data['concentration']} µg/m3)" : '') ."
				</div>
			</div>"
		;

		return $data_return;
	}



	/**
	 * Formatage des données d'indice sous forme d'un widget
	 *
	 * @since     1.0.0
	 * @param     array     $data     Tableau de données
	 * @return    string    Un code HTML représentant les données sous forme de gauge
	 */
	public function format_data_indice_widget($data){

		$data_return =
			"<div class=\"spaa indice_widget indice_{$data['indice num']}\">
				<span class=\"wgt_title\">".str_replace(", ", "<br>", $data['nom'])."</span>
				". (isset( $data['concentration'] ) ? "<span class=\"wgt_molecule\">({$data['abbreviation']})</span>" : '') ."
				{$data['image']}
				<span class=\"wgt_indice\">{$data['indice txt']}</span>
				". (isset( $data['concentration'] ) ? "<span class=\"wgt_concentration\">({$data['concentration']} µg/m3)</span>" : '') ."
			</div>"
		;

		return $data_return;
	}




































}