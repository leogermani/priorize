

    
    <h2><?php echo $pergunta; ?></h2>
    
    <div class="pergunta_container">
        
        <ul>
            <?php while ($opcoes->have_posts()) : $opcoes->the_post(); ?>
                
                <li>
                    <span>Acho mais importante</span>
                    <p><?php the_title(); ?></p>
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
    
        <a class="priorize_voltar">Voltar</a>
    
        <table>
        
            <?php while ($TodasOpcoes->have_posts()) : $TodasOpcoes->the_post(); ?>
                
                <tr>
                    <td><?php the_title(); ?></td>
                    <td><?php echo get_post_meta(get_the_ID(), 'priorize_votes', true); ?></td>
                </tr>

            <?php endwhile; ?>
        
        </table>
    
    </div>
    
    <div class="nova_opcao_container" style="display:none" >
    
        <a class="priorize_voltar">Voltar</a>
    
        <input type="text" class="priorize_nova_opcao_text" />
        
        <a class="priorize_enviar_nova_opcao">Enviar</a>
    
    </div>
    

