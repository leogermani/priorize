
    <h2><?php echo $pergunta; ?></h2>
    
    <?php if( $this->is_question_open( $pergunta_id ) ) : ?>

        <div class="pergunta_container">

            <ul class="pergunta-opcoes">
            
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

    <?php endif; ?>
    
    <div class="results_container" style="<?php echo ( $this->is_question_open( $pergunta_id ) ) ? 'display:none' : 'display:block' ?>" >
    
        <table>
        
            <?php while ($TodasOpcoes->have_posts()) : $TodasOpcoes->the_post(); ?>
                
                <tr>
                    <td><?php the_title(); ?></td>
                    <td><?php echo get_post_meta(get_the_ID(), 'priorize_votes', true); ?></td>
                </tr>

            <?php endwhile; ?>
        
        </table>

        <?php if( $this->is_question_open( $pergunta_id ) ) : ?>
             <a class="priorize_voltar">Voltar</a>
        <?php endif; ?>

    </div>
    
    <?php if( $this->is_question_open( $pergunta_id ) && is_user_logged_in() ) : ?>

        <div class="nova_opcao_container" style="display:none" >

            <input type="text" class="priorize_nova_opcao_text" placeholder="Qual a sua opinião?" />

            <a class="priorize_enviar_nova_opcao">Enviar</a>
        
            <a class="priorize_voltar">Voltar</a>

             <p class="priorize_regras"> <strong>Regras de participação: </strong> </br></br>
                Sua sugestão deve conter até 140 caracteres e ser pertinente à pergunta para entrar em votação.
            </p>
        </div>

    <?php endif; ?>
