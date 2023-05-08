<?php

// Espace de nom du plugin
namespace plugin_spaa;



/**
 * class-admin_page__general
 *
 * Page de paramétrages d'une page d'administration
 * Page : Général
 * Page d'administration principale
 * Paramètres créés :
 * 	cle_api
 * 	code_insee
 * 	delai_peremption
 *
 * Ce fichier est appelé au sein de la classe admin
 *
 * @link       https://www.arthurbazin.com
 * @since      1.0.0
 *
 * @author     Arthur Bazin
 */



/**
 * Classe de la page d'administration : Général
 *
 * @since      1.0.0
 * @author     Arthur Bazin
 */
class admin_page__general {

	/**
	 * Objet contenant la classe parente
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object       $parent_object
	 */
	public $parent_object;

	/**
	 * Nom de la page
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array       $page_name
	 */
	public $page_name = 'Shortcode pour Atmo AuRA';

	/**
	 * Nom de la page
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array       $page_name
	 */
	public $page_slug = 'spaa_general';



	/**
	 * Mise en place de l'admin
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct($init_parent_object) {

		$this->parent_object = $init_parent_object;

		$this->init_page();

		$this->init_page_content();

	}



	/**
	 * Initialisation de la page
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function init_page() {

		$page_option = add_submenu_page(
			"options-general.php",                                                   // Page parente (dans le menu)
			$this->page_name,                                                        // Titre de la page
			$this->parent_object->parent_object->get_plugin_name(),                  // Titre du menu
			'manage_options',                                                        // Privilège de l'utilisateur pour y accéder
			$this->page_slug,                                                        // Slug de la page
			array($this, 'display_page'),                                            // Fonction callback d'affichage
			10                                                                       // Priorité/position.
		);

		// Ajout d'une aide
		add_action( 'load-' . $page_option, array($this, 'display_help') );

	}



	/**
	 * Contenu de la page
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	function display_page(){
		?>
			<div class="wrap">
				<!-- Displays the title -->
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<!-- The form must point to options.php -->
				<form action="options.php" method="POST">
					<?php 
						// Output the necessary hidden fields : nonce, action, and option page name
						settings_fields( $this->parent_object->plugin_setting_group );
						// Boucle sur chaque section et champ enregistré pour cette page
						do_settings_sections( $this->page_slug );
						// Afficher le bouton de validation
						submit_button();
					?>
				</form>
			</div>
		<?php
	}



	/**
	 * Ajout d'une aide pour la page
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	function display_help() {

		// Récupération de la page courante
		$screen = get_current_screen();

		// Ajout de l'onglet d'aide
		$screen->add_help_tab(
			array(
				'id' => $this->page_slug.'_help',
				'title' => __( 'Aide' ),
				'content' => 
"<p>L'utilisation se fait simplement en plaçant le shortcode suivant : [spaa]</p>

<p>Plusieurs paramètres sont disponibles afin de spécifier les données voulues :
	<ul>
		<li>
			<code>indicateur</code> : \"vigilance\" ou \"indice\".
			<ul>
				<li>
					<code>vigilance</code> : renvoi un bloc HTML détaillant les vigilances pollution. Les balises utilisées sont <code>&lt;p&gt;</code> s'il n'y a aucune vigilance (une simple phrase), <code>&lt;ul&gt;</code> et <code>&lt;li&gt;</code> s'il y a une ou plusieurs vigilances (liste de vigilance).
				</li>
				<li>
					<code>indice</code> : defaut : indice de pollution. A utiliser avec les paramètres suivants : echeance et parametre.
				</li>
			</ul>
		</li>
		<li>
			<code>echeance</code> : uniquement avec le paramètre indicateur=indice.
			<ul>
				<li>
					<code>n</code> : defaut : valeurs de pollution pour le jour même.
				</li>
				<li>
					<code>n+1</code> : valeurs de pollution pour le jour suivant.
				</li>
			</ul>
		</li>
		<li>
			<code>parametre</code> : uniquement avec le paramètre indicateur=indice.
			<ul>
				<li>
					<code>global_valeur</code> : Valeur du paramètre.
				</li>
				<li>
					<code>global_indice</code> : defaut : Indice.
				</li>
				<li>
					<code>global_couleur</code> : Code couleur hexadecimal.
				</li>
				<li>
					<code>PM2.5</code> : Code HTML contenant les données de pollution au microparticule inférieur à 2,5 micron.
				</li>
				<li>
					<code>PM10</code> : Code HTML contenant les données de pollution au microparticule inférieur à 10 micron.
				</li>
				<li>
					<code>SO2</code> : Code HTML contenant les données de pollution au dioxyde de souffre.
				</li>
				<li>
					<code>O3</code> : Code HTML contenant les données de pollution à l'ozone.
				</li>
				<li>
					<code>NO2</code> : Code HTML contenant les donnée de pollution au dioxyde d'azote.
				</li>
			</ul>
		</li>
		<li>
			<code>debug</code> : utilisé sans valeur, les données bruttes sont renvoyées. Ce paramètre prime sur tous les autres.
		</li>
	</ul>
</p>

<p>Voici quelques exemples d'utilisation du shortcode :
<ul>
	<li>
		<code>[spaa]</code><br>
		=> equivalent à <code>[spaa indicateur=\"indice\" echeance=\"n\" parametre=\"global_indice\"]</code>
	</li>

	<li>
		<code>[spaa echeance=\"n+1\" parametre=\"PM2.5\"]</code><br>
		=>equivalent à <code>[spaa indicateur=\"indice\" echeance=\"n+1\" parametre=\"PM2.5\"]</code>
	</li>

	<li>
		<code>[spaa echeance=\"n+1\" parametre=\"PM2.5\" debug]</code><br>
		=>equivalent à <code>[spaa debug]</code> (<code>debug</code> prime sur tout autre paramètre).
	</li>

	<li>
		<code>[spaa indicateur=\"vigilance\"]</code>
	</li>
</ul>
</p>
"
			)
		);

		// Ajout d'une sidebar supplémentaire
		//$screen->set_help_sidebar( __( 'Hello Dolly' ) );
	}



	/**
	 * Ajout du contenu de la page
	 * Ajout des sections et des champs
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	function init_page_content(){

		// Ajout d'une section
		add_settings_section( 
			'setting_'.($this->page_slug).'_section_parametre',                 // ID de la section
			'Paramètres',                                                       // Titre
			'',                                                                 // Fonction callback
			$this->page_slug,                                                   // Slug de la page dans laquelle afficher la section
			array(
				'before_section' => '',                                        // HTML a ajouter avant la section
				'after_section' => '',                                         // HTML a ajouter après la section
				'section_class' => ''                                          // Classe de la section
			)
		);


		// Ajout d'un paramètre
		add_settings_field( 
			'setting_'.($this->page_slug).'_setting_cle_api',              // ID du champ
			'Clé d\'API',                                                  // Titre
			array($this, 'display_setting_cle_api'),                       // Fonction callback
			$this->page_slug,                                              // Page
			'setting_'.($this->page_slug).'_section_parametre',            // Section
			//array( 
			//	'label_for' => '',                                         // Id for the input and label element.
			//)
		);


		// Ajout d'un paramètre
		add_settings_field( 
			'setting_'.($this->page_slug).'_setting_code_insee',          // ID du champ
			'Code INSEE de la commune',                                   // Titre
			array($this, 'display_setting_code_insee'),                                 // Fonction callback
			$this->page_slug,                                              // Page
			'setting_'.($this->page_slug).'_section_parametre',            // Section
			//array( 
			//	'label_for' => '',                                         // Id for the input and label element.
			//)
		);


		// Ajout d'un paramètre
		add_settings_field( 
			'setting_'.($this->page_slug).'_setting_delai_peremption',          // ID du champ
			'Délais de péremption des données (en seconde)',                                   // Titre
			array($this, 'display_setting_delai_peremption'),                                 // Fonction callback
			$this->page_slug,                                              // Page
			'setting_'.($this->page_slug).'_section_parametre',            // Section
			//array( 
			//	'label_for' => '',                                         // Id for the input and label element.
			//)
		);

	}


	/**
	 * Affichage d'un champ 
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @param    array  $args    Arguments récupérer depuis l'appel dans la fonction add_settings_field()
	*/
	public function display_setting_cle_api( $args ){

		$setting_name = $this->parent_object->plugin_setting_name;
		$sub_setting_name = 'cle_api';

		$setting = get_option( $setting_name );
		$value = ! empty( $setting[$sub_setting_name] ) ? $setting[$sub_setting_name] : '';
		$label = ! empty( $args['label_for'] ) ? $args['label_for'] : '';

		?>
			<input id="<?php echo esc_attr( $label ); ?>" class="regular-text" type="text" name="<?php echo esc_attr( $setting_name.'['.$sub_setting_name.']' ); ?>" value="<?php echo esc_attr( $value ); ?>"><br/>
			<p>La clé d'API peut être récupérée sur le site d'Atmo Auvergne Rhône Alpes : <a href="http://api.atmo-aura.fr/documentation" target="_blank">https://api.atmo-aura.fr/documentation</a></p>
		<?php
	}


	/**
	 * Affichage d'un champ 
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @param    array  $args    Arguments récupérer depuis l'appel dans la fonction add_settings_field()
	*/
	public function display_setting_code_insee( $args ){

		$setting_name = $this->parent_object->plugin_setting_name;
		$sub_setting_name = 'code_insee';

		$setting = get_option( $setting_name );
		$value = ! empty( $setting[$sub_setting_name] ) ? $setting[$sub_setting_name] : '';
		$label = ! empty( $args['label_for'] ) ? $args['label_for'] : '';

		?>
			<input id="<?php echo esc_attr( $label ); ?>" class="regular-text" type="text" name="<?php echo esc_attr( $setting_name.'['.$sub_setting_name.']' ); ?>" value="<?php echo esc_attr( $value ); ?>"><br/>
			<p>Le code INSEE est composé de 5 chiffres</p>
		<?php
	}


	/**
	 * Affichage d'un champ 
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @param    array  $args    Arguments récupérer depuis l'appel dans la fonction add_settings_field()
	*/
	public function display_setting_delai_peremption( $args ){

		$setting_name = $this->parent_object->plugin_setting_name;
		$sub_setting_name = 'delai_peremption';

		$setting = get_option( $setting_name );
		$value = ! empty( $setting[$sub_setting_name] ) ? $setting[$sub_setting_name] : '';
		$label = ! empty( $args['label_for'] ) ? $args['label_for'] : '';

		?>
			<input id="<?php echo esc_attr( $label ); ?>" class="regular-text" type="number" name="<?php echo esc_attr( $setting_name.'['.$sub_setting_name.']' ); ?>" value="<?php echo esc_attr( $value ); ?>"><br/>
			<p>Le délais de péremption est par défaut de 3h (10800 secondes). Au delà, l'API est recontactée pour actualiser les données.</p>
		<?php
	}


}