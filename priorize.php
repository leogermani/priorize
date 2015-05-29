<?php
/*
Plugin Name: Priorize
Plugin URI: 
Description: 
Author: leogermani
Version: 1.0
Text Domain:
*/

function priorize_init() {
	
	class Priorize {
		
		function Priorize() {
			
			$pluginFolder = plugin_basename( dirname(__FILE__) );
			
			load_plugin_textdomain('ph', "wp-content/plugins/$pluginFolder/languages", "$pluginFolder/languages");
            
            $this->basepath =  WP_CONTENT_DIR . "/plugins/$pluginFolder/";
            $this->baseurl = WP_CONTENT_URL . "/plugins/$pluginFolder/"; 
            
            register_deactivation_hook(__FILE__, array(&$this, 'deactivate'));
            
        
            add_action('wp_print_scripts',array(&$this, 'addJS'));
            add_action('wp_print_styles',array(&$this, 'addCSS'));
            
            $this->registerPostType();
            $this->registerTaxonomy();
            
            add_action('wp_ajax_priorize_outras', array(&$this, 'ajax_outras_opcoes'));
            add_action('wp_ajax_nopriv_priorize_outras', array(&$this, 'ajax_outras_opcoes'));
            
            add_action('wp_ajax_priorize_vote', array(&$this, 'ajax_process_vote'));
            add_action('wp_ajax_nopriv_priorize_vote', array(&$this, 'ajax_process_vote'));
			
            add_action('wp_ajax_priorize_nova_opcao', array(&$this, 'ajax_add_option'));
            
		}
		
		function deactivate() {
			return;
		}
        
		function addJS() {
            wp_enqueue_script('priorize', $this->baseurl . 'js/priorize.js');
            wp_localize_script('priorize', 'pr', array('loadingMessage' => __('Loading', 'ph'), 'ajaxurl' => admin_url('admin-ajax.php')));
        }
        
        function addCSS() {
            wp_enqueue_style('priorize', $this->baseurl . 'css/style.css');
        }
		
        function registerPostType() {
            register_post_type('opcao', array(
                'labels' => array(
                    'name' => _x('Opções', 'post type general name', 'SLUG'),
                    'singular_name' => _x('Opção', 'post type singular name', 'SLUG'),
                    'add_new' => _x('Adicionar Novo', 'image', 'SLUG'),
                    'add_new_item' => __('Adicionar novo Opção', 'SLUG'),
                    'edit_item' => __('Editar Opção', 'SLUG'),
                    'new_item' => __('Novo Opção', 'SLUG'),
                    'view_item' => __('Ver Opção', 'SLUG'),
                    'search_items' => __('Search Opçãos', 'SLUG'),
                    'not_found' => __('Nenhum Opção Encontrado', 'SLUG'),
                    'not_found_in_trash' => __('Nenhum Opção na Lixeira', 'SLUG'),
                    'parent_item_colon' => ''
                ),
                'public' => true,
                //'rewrite' => array('slug' => '_POST_TYPE_'),
                'capability_type' => 'post',
                'hierarchical' => true,
                'map_meta_cap ' => true,
                'menu_position' => 6,
                'has_archive' => false, //se precisar de arquivo
                'supports' => array(
                    'title',
                    //'editor',
                    //'page-attributes',
                    'author'
                ),
                // 'taxonomies' => array('taxonomia')
                )
            );
        }
        
        function registerTaxonomy() {
            
            $labels = array(
                'name' => _x( 'Perguntas', 'taxonomy general name', 'SLUG' ),
                'singular_name' => _x( 'Pergunta', 'taxonomy singular name', 'SLUG' ),
                'search_items' =>  __( 'Search Perguntas', 'SLUG' ),
                'all_items' => __( 'All Perguntas', 'SLUG' ),
                'parent_item' => __( 'Parent Pergunta', 'SLUG' ),
                'parent_item_colon' => __( 'Parent Pergunta:', 'SLUG' ),
                'edit_item' => __( 'Edit Pergunta', 'SLUG' ), 
                'update_item' => __( 'Update Pergunta', 'SLUG' ),
                'add_new_item' => __( 'Add New Pergunta', 'SLUG' ),
                'new_item_name' => __( 'New Pergunta Name', 'SLUG' ),
            ); 	

            register_taxonomy('perguntas','opcao', array(
                    'hierarchical' => true,
                    'labels' => $labels,
                    'show_ui' => true,
                    'query_var' => true,
                    'rewrite' => true,
                    'show_admin_column' => true,
                )
            );
            
            require_once('taxonomy_single_term/class.taxonomy-single-term.php');
            $custom_tax_mb = new Taxonomy_Single_Term( 'perguntas' );
            
        }
        
        function the_content($pergunta_id) {
            
            $taxonomy = get_term($pergunta_id, 'perguntas');
            
            $pergunta = $taxonomy->name;
            
            $opcoes = $this->get_random_pair($pergunta_id);
            $TodasOpcoes = $this->get_results($pergunta_id);
            
            $opiniao_link = current_user_can('edit_posts') ? '' : 'href="' . wp_login_url() . '"';
            
            //var_dump($opcoes);
            
            if (!defined('DOING_AJAX') || DOING_AJAX == false) echo '<article id="pergunta_', $pergunta_id, '" class="priorize_pergunta">';
            
            include('template.php');
            
            if (!defined('DOING_AJAX') || DOING_AJAX == false) echo '</article>';
            
            wp_reset_query();
            
        }
        
        function get_random_pair($pergunta_id) {
            
            return new WP_Query(array(
                'post_type' => 'opcao',
                'posts_per_page' => 2,
                'orderby' => 'rand',
                'post_status' => 'publish',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'perguntas',
                        'terms' => $pergunta_id
                    )
                )
            ));
            
        }
        
        function get_results($pergunta_id) {
            
            return new WP_Query(array(
                'post_type' => 'opcao',
                'posts_per_page' => -1,
                'orderby' => 'meta_value',
                'order' => 'DESC',
                'meta_key' => 'priorize_votes',
                'post_status' => 'publish',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'perguntas',
                        'terms' => $pergunta_id
                    )
                )
            ));
            
        }
        
        function add_vote($opcao_id) {
            
            $current = get_post_meta($opcao_id, 'priorize_votes', true);
            if (!$current) $currrent = 0;
            $current = intval($current);
            $current++;
            delete_post_meta($opcao_id, 'priorize_votes');
            add_post_meta($opcao_id, 'priorize_votes', $current);
            
        }
        
        function ajax_process_vote() {
            if (isset($_POST['opcao_id']) && $_POST['opcao_id'] != '') {
                $this->add_vote($_POST['opcao_id']);
            }
            $this->the_content($_POST['pergunta_id']);
            die;
        }
        
        function ajax_outras_opcoes() {
            if (isset($_POST['pergunta_id']) && $_POST['pergunta_id'] != '') {
                $this->the_content($_POST['pergunta_id']);
            }
            die;
            
        }
        
        function ajax_add_option() {
            $cur_user = wp_get_current_user();
            
            if (current_user_can('edit_posts') && isset($_POST['nova_opcao']) && $_POST['nova_opcao'] != '') {
                $newPost = array(
                    'post_type' => 'opcao',
                    'post_title' => $_POST['nova_opcao'],
                    'post_author' => $cur_user->ID,
                    'post_status' => 'draft',
                    'tax_input' => array('perguntas' => array($_POST['pergunta_id']))
                );
                wp_insert_post($newPost);
            }
            
            $this->the_content($_POST['pergunta_id']);
            
            die;
            
        }
        
        
        
	}
	
	global $Priorize;
    $Priorize = new Priorize();
    
    function priorize_pergunta_shortcode ($atts, $content) {
        
        // TODO...
        
        global $Priorize;
        
        
        
        return $embed;
        
    }
	
	add_shortcode('priorize_pergunta', 'priorize_pergunta_shortcode');
	
}

add_action('init', 'priorize_init', 5);

register_activation_hook(__FILE__, 'priorize_activate');

function priorize_activate() {
    return;
}

function priorize_print_pergunta($pergunta_id){
    
    global $Priorize;
    
    $Priorize->the_content($pergunta_id);
    
}


?>
