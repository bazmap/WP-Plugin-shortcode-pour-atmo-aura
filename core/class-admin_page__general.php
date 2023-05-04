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

		add_submenu_page(
			"options-general.php",                                                   // Page parente (dans le menu)
			$this->page_name,                                                        // Titre de la page
			$this->parent_object->parent_object->get_plugin_name(),                  // Titre du menu
			'manage_options',                                                        // Privilège de l'utilisateur pour y accéder
			$this->page_slug,                                                        // Slug de la page
			array($this, 'display_page'),                                            // Fonction callback d'affichage
			10                                                                       // Priorité/position.
		);

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

		// Registers a text field example
		add_settings_field( 
			'setting_'.($this->page_slug).'_setting_cle_api',              // ID du champ
			'Clé d\'API',                                                  // Titre
			array($this, 'display_setting_cle_api'),                       // Fonction callback
			$this->page_slug,                                              // Page
			'setting_'.($this->page_slug).'_section_parametre',            // Section
			//array( 
			//	'label_for' => 'settings_boilerplate_text_field',          // Id for the input and label element.
			//)
		);

		// Registers a text field example
		add_settings_field( 
			'setting_'.($this->page_slug).'_setting_code_insee',          // ID du champ
			'Code INSEE de la commune',                                   // Titre
			array($this, 'display_setting_code_insee'),                                 // Fonction callback
			$this->page_slug,                                              // Page
			'setting_'.($this->page_slug).'_section_parametre',            // Section
			//array( 
			//	'label_for' => 'settings_boilerplate_text_field',          // Id for the input and label element.
			//)
		);

	}


	/**
	 * Affichage d'un champ 
	 * Voir pour faire un id général dans une variable ?
	 * Voir pour faire une fonction standard pour la première partie de display_s1_c1
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
	 * Voir pour faire un id général dans une variable ?
	 * Voir pour faire une fonction standard pour la première partie de display_s1_c1
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


}