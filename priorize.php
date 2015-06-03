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
            add_action( 'admin_enqueue_scripts', array(&$this, 'adminAddCss' ));
            
            $this->registerPostType();
            $this->registerTaxonomy();

            // adiciona campo na taxonomia
            add_action('perguntas_edit_form', array( &$this, 'perguntas_edit_form' ));
            add_action('perguntas_add_form', array( &$this, 'perguntas_edit_form' ));

            add_action( 'perguntas_add_form_fields', array( &$this, 'add_tax_date_field' ));
            add_action( 'perguntas_edit_form_fields', array( &$this, 'edit_tax_date_field'  ));

            // saving
            add_action( 'edited_perguntas', array( &$this, 'save_tax_meta'), 10, 2 );
            add_action( 'create_perguntas', array( &$this, 'save_tax_meta'), 10, 2 );


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
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('priorize-counter', $this->baseurl . 'js/jquery.counter.js' );
            wp_enqueue_script('priorize', $this->baseurl . 'js/priorize.js');
            wp_localize_script('priorize', 'pr', array('loadingMessage' => __('Loading', 'ph'), 'ajaxurl' => admin_url('admin-ajax.php')));
        }
        
        function addCSS() {
            wp_enqueue_style('priorize', $this->baseurl . 'css/style.css');
            wp_enqueue_style('counter-style',  $this->baseurl . 'css/jquery.counter-analog3.css');
        }

        function adminAddCss() {
            wp_enqueue_style('jquery-ui-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
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
            
            $opiniao_link = current_user_can('edit_posts') ? '' : 'href="' . wp_login_url( $_SERVER['REQUEST_URI'] ) . '"';
            
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
                'orderby' => 'meta_value_num',
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

        /**
         * Adds campos extras no formulário inicial da taxonomia
         *
         */
        function add_tax_date_field(){
            $date = new DateTime();
            $interval = new DateInterval('P1M');
            $date->add($interval);

        ?>
            <div class="form-field">
                <label for="term_meta[tax_date]">Data encerramento</label>
                <input type="text" name="term_meta[tax_date]" id="term_meta[tax_date]" class="select_date" value="<?php echo $date->format('d/m/Y'); ?>" maxlength="10" size="10"/>
                <p class="description">Informe a data que a pergunta será encerrada</p>
            </div><!-- /.form-field -->
        <?php
        }


        /**
         * Adds campos extras no formulário de edição da taxonomia
         *
         */
        function edit_tax_date_field( $term ){

            $date = new DateTime();
            $interval = new DateInterval('P1M');
            $date->add($interval);

            $term_id = $term->term_id;
            $term_meta = get_option( "taxonomy_$term_id" );
            $data_encerramento = $term_meta['tax_date'] ? $term_meta['tax_date'] : $date->format('d/m/Y');

            ?>
            <tr class="form-field">
                <th scope="row">
                    <label for="term_meta[tax_date]">Data encerramento</label>
                    <td>
                        <input type="text" name="term_meta[tax_date]" id="term_meta[tax_date]" class="select_date" value="<?php if( !empty( $data_encerramento ) ) print date( 'd/m/Y', strtotime( $data_encerramento ) ); ?>" maxlength="10" size="10"/>
                        <p class="description">Informe a data que a pergunta será encerrada</p>                         
                    </td>
                </th>
            </tr><!-- /.form-field -->
         <?php
        } // edit_tax_image_field


        /**
         * Salva os dados dos campos extras das taxonomias
         *
         */
        function save_tax_meta( $term_id ){
         
            if ( isset( $_POST['term_meta'] ) ) {
         
                $term_meta = array();
         
                $term_meta['tax_date'] = isset ( $_POST['term_meta']['tax_date'] ) ? esc_html( $_POST['term_meta']['tax_date'] ) : '';
                
                //formato da data
                if( preg_match( '/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/', $term_meta['tax_date'] ) )
                    $term_meta['tax_date'] = preg_replace( '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', '$3-$2-$1', $term_meta['tax_date'] );

                // Save the option array.
                update_option( "taxonomy_$term_id", $term_meta );
         
            } // if isset( $_POST['term_meta'] )
        } // save_tax_meta



        /**
         * Carrega o script apenas na página do formulário da taxonomia
         *
         */
        function perguntas_edit_form() {
            ?>
            <script type="text/javascript">
            jQuery(document).ready(function(){

                jQuery('.select_date').datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                    nextText: 'Próximo',
                    prevText: 'Anterior'
                });

                    });
            </script>

            <?php 
        }

        /**
         * Verifica se a pergunta está aberta
         *
         */
        function is_question_open( $pergunta_id ) {

            if( empty( $pergunta_id ) )
                return false;

            $today = gmdate( 'Y-m-d', ( time() + ( get_option( 'gmt_offset' ) * 3600 ) ) );

            $term_meta = get_option( "taxonomy_$pergunta_id" );
            $data_encerramento = $term_meta['tax_date'] ? $term_meta['tax_date'] : '';

            if( $today > $data_encerramento || empty( $data_encerramento ) )
                return false;
            else
                return true;
        }

         /**
         * Cronômetro
         *
         */
        function cronometro( $pergunta_id ) {

            $term_meta = get_option( "taxonomy_$pergunta_id" );
            $datafinal = strtotime( $term_meta['tax_date'] ? $term_meta['tax_date'] : '');
                    
            if ($datafinal) {
                if ($datafinal > time()) {
                    $intervalo = $datafinal - time();
                    $dias = $intervalo / 60 / 60 / 24;
                    $dias = (int) $dias + 1;
                } else {
                    $dias = -1;
                }
            } 


            if ($dias > 0) 
                $texto_cronometro = sprintf( 'Falta%1$s apenas <span class="count" data-stop="%2$s">%2$s</span> dia%3$s para o fim da enquete', ( $dias > 1 ) ? "m" : "", $dias, ( $dias > 1 ) ? "s" : "" );
            elseif ( date( 'Y-m-d', $datafinal ) == date('Y-m-d') )
                $texto_cronometro = "A enquete se encerra hoje";
            else    
                $texto_cronometro =  "Enquete encerrada";
           
            return "<p class='cronometro'>$texto_cronometro</p>";
   
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

    $taxonomy = get_term( $pergunta_id, 'perguntas');
    
    $pergunta = $taxonomy->name;

    echo "<div class='priorize_container'>";

        do_action( 'priorize_template_before', $pergunta_id, $pergunta );

        $Priorize->the_content($pergunta_id);

        echo $Priorize->cronometro( $pergunta_id );

        do_action( 'priorize_template_after', $pergunta_id, $pergunta );

    echo "</div>";
}

?>
