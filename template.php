
<?php do_action( 'priorize_template_before', $pergunta_id, $pergunta );  ?>

<div class="pergunta_content">

    <h2><?php echo $pergunta; ?></h2>
    
    <div class="pergunta_container">
        
        <ul class="pergunta-opcoes">

            <!-- <span class="divider">OU</span> -->
        
            <?php while ($opcoes->have_posts()) : $opcoes->the_post(); ?>
                
                <li class="pergunta-opcao">
                    <span>Acho mais importante</span>
                    <p class="opcao-titulo"><?php the_title(); ?></p>
                    <a id="vote-for-<?php the_ID(); ?>" class="priorize_vote">Vote</a>
                </li>

            <?php endwhile; ?>
        </ul>

        <div class="pergunta_controls">
        
            <a <?php echo $opiniao_link; ?> class="priorize_opiniao">Dê sua Opinião</a>
            
            <a class="priorize_outras">Outras Opções</a>
            
            <a class="priorize_resultados">Ver Resultados</a>
        
        </div>
        
        <div class="pergunta_feedback">
        
            <div class="priorize_loading">Carregando</div>
            <div class="priorize_sucesso">Agradecemos sua participação</div>
            <div class="priorize_erro">Erro</div>
        
        </div>

    </div>
    
    <div class="results_container" style="display:none" >
    
        <table>
        
            <?php while ($TodasOpcoes->have_posts()) : $TodasOpcoes->the_post(); ?>
                
                <tr>
                    <td><?php the_title(); ?></td>
                    <td><?php echo get_post_meta(get_the_ID(), 'priorize_votes', true); ?></td>
                </tr>

            <?php endwhile; ?>
        
        </table>

         <a class="priorize_voltar">Voltar</a>
    
    </div>
    
    <div class="nova_opcao_container" style="display:none" >

        <input type="text" class="priorize_nova_opcao_text" placeholder="Qual a sua opinião?" />

        <a class="priorize_enviar_nova_opcao">Enviar</a>
    
        <a class="priorize_voltar">Voltar</a>

         <p class="priorize_regras"> Regras de participação: </br>
            Sua sugestão deve conter até 140 caracteres e ser pertinente à pergunta para entrar em votação.
        </p>
    </div>
    
</div>

 <?php do_action( 'priorize_template_after', $pergunta_id, $pergunta );  ?>
