<?php

// Espace de nom du plugin
namespace plugin_spaa;



/**
 * class-spaa_admin
 *
 * Définition des options d'administration du plugin
 *
 * @link       https://www.arthurbazin.com
 * @since      1.0.0
 *
 * @author     Arthur Bazin
 */



/**
 * Classe d'administration
 *
 * Cette classe permet d'initialiser et de gérer tous les éléments de l'administration du plugin
 *
 * @since      1.0.0
 * @author     Arthur Bazin
 */
class admin {

	/**
	 * Objet contenant la classe parente
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object       $parent_object
	 */
	public $parent_object;

	/**
	 * Nom du paramètre du plugin
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object       $plugin_setting_name
	 */
	public $plugin_setting_name;

	/**
	 * Nom du groupe de paramètre du plugin
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object       $plugin_setting_group
	 */
	public $plugin_setting_group;



	/**
	 * Mise en place de l'admin
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct($init_parent_object) {

		// Définition des variables
		$this->parent_object = $init_parent_object;
		$this->plugin_setting_name = ($this->parent_object->get_plugin_abrv()).'_settings';
		$this->plugin_setting_group = ($this->parent_object->get_plugin_abrv()).'_settings';


		$this->init_plugin_setting();
		$this->init_admin();
	}



	/**
	 * Initialisation de l'administration
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function init_admin() {
		if ( ! empty ( $GLOBALS['pagenow'] )
		and 
			( 
				'options-general.php' === $GLOBALS['pagenow']
				or 'options.php' === $GLOBALS['pagenow']
			)
		) {
			add_action( 'admin_init', array($this, 'init_plugin_setting'));
		}
		add_action('admin_menu', array($this, 'init_pages'));
	}



	/**
	 * Enregistrement d'un paramètre qui stockera tous les paramètres de l'application
	 * Il est possible d'enregistrer plusieurs paramètres mais on utilisera un seul paramètre de type tableau
	 * Cela permet de limiter les appels à la base de données
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	function init_plugin_setting(){

		register_setting( 
			$this->plugin_setting_group,                   // Groupe
			$this->plugin_setting_name,                    // Nom
			array($this, 'settings_sanitizer')             // Fonction de nettoyage
		);

	}



	/**
	 * Initialisation de l'administration
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function init_pages() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'core/class-admin_page__general.php';

		$class_admin_page_general = new admin_page__general($this);

	}



	/**
	 * Assainissement des paramètres
	 * 
	 * @param  array  $settings     Paramètre
	*/
	function settings_sanitizer( $settings ){

		$var_type = gettype($settings);

		if ( $var_type == 'string' ) {
			$return_settings = sanitize_textarea_field($settings);
		}

		elseif ( in_array($var_type, array('integer', 'double')) ) {
			$return_settings = $settings;
		}

		elseif ( $var_type == 'boolean' ) {
			$return_settings = $settings;
		}

		elseif ( $var_type == 'array' ) {
			foreach ($settings as $key => $value) {
				$return_settings[$key] = sanitize_textarea_field($value);
			}
		}

		else {
			$return_settings = '';
		}

		return $return_settings;
	}

}

