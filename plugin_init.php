<?php

// Espace de nom du plugin
namespace plugin_spaa;



/**
 * Fichier de démarrage
 *
 * @link              https://www.arthurbazin.com
 * @since             1.0.0
 * @package           Shortcode_Pour_Atmo_Auvergne_Rhone_Alpes
 *
 * @wordpress-plugin
 * Plugin Name:       Shortcode pour Atmo Auvergne-Rhône-Alpes
 * Plugin URI:        https://www.plugin.com
 * Description:       Plugin NON-OFFICIEL d'affichage des données d'Atmo Auvergne-Rhône-Alpes au sein de WordPress.
 * Version:           0.1.0
 * Author:            Arthur Bazin
 * Author URI:        https://www.arthurbazin.com
 * Licence:           GPL2
 * Licence URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */



// Si le fichier est appelé directement, on stoppe toute execution.
if ( !defined('ABSPATH') ) {
	exit;
}



// Class de définition principale du plugin.
require plugin_dir_path( __FILE__ ) . 'core/class-core.php';


/**
 * Création de l'instance du plugin.
 * Paramètres :
 *   Le nom
 *   Le slug
 *   Le nom abrégé
 *   La version
 */
$plugin_spaa = new core(
	'Shortcode pour Atmo AuRA', 
	'shortcode_pour_atmo_aura', 
	'spaa', 
	'0.1.0'
);

// Démarrage du plugin
$plugin_spaa->run();

