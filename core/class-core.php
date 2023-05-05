<?php

// Espace de nom du plugin
namespace plugin_spaa;



/**
 * class-core
 *
 * Fichier contenant la classe principale du plugin
 *
 * @link       https://www.arthurbazin.com
 * @since      1.0.0
 *
 * @author     Arthur Bazin
 */



/**
 * Classe principale du plugin
 *
 * Cette classe est le plugin en lui-même
 *
 * @since      1.0.0
 * @author     Arthur Bazin
 */
class core {

	/**
	 * Nom du plugin
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name
	 */
	public $plugin_name;

	/**
	 * Nom du plugin slugifié
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_slug_name
	 */
	public $plugin_slug_name;

	/**
	 * Nom abrégé du plugin
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_abrv_name
	 */
	public $plugin_abrv_name;

	/**
	 * Version actuelle du plugin (au format semver)
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_version
	 */
	public $plugin_version;

	/**
	 * Objet contenant tous les éléments d'administration du plugin
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      object    $plugin_object_admin
	 */
	public $plugin_object_admin;



	/**
	 * Mise en place du plugin
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param    string    $init_plugin_name            Nom du plugin.
	 * @param    string    $init_plugin_abrv_name       Nom abbrégé du plugin
	 * @param    string    $init_plugin_version         Version du plugin
	 */
	public function __construct(
		string $init_plugin_name, 
		string $init_plugin_slug_name, 
		string $init_plugin_abrv_name, 
		string $init_plugin_version
	) {

		// Définition des variables de base
		$this->plugin_name = $init_plugin_name;
		$this->plugin_slug_name = $init_plugin_slug_name;
		$this->plugin_abrv_name = $init_plugin_abrv_name;
		$this->plugin_version = $init_plugin_version;

		// Ajout des styles pour la page d'admin
		add_action('admin_enqueue_scripts', array($this, 'enqueue_custom_admin_style'));

	}



	/**
	 * Démarrage du plugin
	 *
	 * @since    1.0.0
	 */
	public function run() {
		
		// Menu d'administration
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'core/class-admin.php';

		$this->plugin_object_admin = new admin($this);


		// Fonctionnalités
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'functions/class-functionalities.php';

		new functionalities($this);

	}



	/**
	 * Enregistrement du style CSS
	 *
	 * @since     1.0.0
	 */
	function enqueue_custom_admin_style( $hook ) {

		// Uniquement sur la page de paramètre du plugin
		if ( $hook === 'settings_page_spaa_general' ) {
			wp_register_style( 
				'spaa_admin_css', 
				plugin_dir_url( dirname( __FILE__ ) ) . 'css/admin_style.css', 
				false, 
				'1.0.0' 
			);

			wp_enqueue_style( 'spaa_admin_css' );
		}
	}




	/**
	 * Récupération du nom du plugin.
	 *
	 * @since     1.0.0
	 * @return    string    Le nom du plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}



	/**
	 * Récupération de l'abréviation du plugin.
	 *
	 * @since     1.0.0
	 * @return    string    L'abbréviation de version du plugin.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug_name;
	}



	/**
	 * Récupération de l'abréviation du plugin.
	 *
	 * @since     1.0.0
	 * @return    string    L'abbréviation de version du plugin.
	 */
	public function get_plugin_abrv() {
		return $this->plugin_abrv_name;
	}



	/**
	 * Récupération du numéro de version du plugin.
	 *
	 * @since     1.0.0
	 * @return    string    Le numéro de version du plugin.
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}


}