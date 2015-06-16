jQuery(document).ready(function($) {
    $('.priorize_outras').live('click', function() {
        var pergunta_id = $(this).parents('.priorize_pergunta').attr('id').replace('pergunta_', '');
        priorize_loading(pergunta_id);
        $.post(
            pr.ajaxurl, 
            {
                action: 'priorize_outras',
                pergunta_id: pergunta_id
            },
            function(response) {
                
                console.log(response);
                $('#pergunta_' + pergunta_id).html(response);
                
                priorize_hideLoading(pergunta_id);
                
            });
    });
    
    $('.priorize_vote').live('click', function() {
        var pergunta_id = $(this).parents('.priorize_pergunta').attr('id').replace('pergunta_', '');
        var opcao_id = $(this).attr('id').replace('vote-for-', '');
        priorize_loading(pergunta_id);
        $.post(
            pr.ajaxurl, 
            {
                action: 'priorize_vote',
                pergunta_id: pergunta_id,
                opcao_id: opcao_id
            },
            function(response) {
                
                $('#pergunta_' + pergunta_id).html(response);
                
                priorize_hideLoading(pergunta_id);
                priorize_show_message(pergunta_id, ".priorize_sucesso");
                
            });
    });
    
    $('.priorize_enviar_nova_opcao').live('click', function() {
        var pergunta_id = $(this).parents('.priorize_pergunta').attr('id').replace('pergunta_', '');
        var nova_opcao = $('#pergunta_' + pergunta_id).find('.priorize_nova_opcao_text').val();
        if (nova_opcao == '') {
            alert('Insira um texto para sua sugestão');
        } else {
            priorize_loading(pergunta_id);
            $.post(
                pr.ajaxurl, 
                {
                    action: 'priorize_nova_opcao',
                    pergunta_id: pergunta_id,
                    nova_opcao: nova_opcao
                },
                function(response) {
                    $('#pergunta_' + pergunta_id).html(response);

                    priorize_hideLoading(pergunta_id);
                    priorize_show_message(pergunta_id, ".priorize_sucesso", "Agradecemos sua participação. Em breve sua contribuição entrará na votação.");
                    
            });
        }
    });
    
    $('.priorize_voltar').live('click', function() {
        var pergunta_id = $(this).parents('.priorize_pergunta').attr('id').replace('pergunta_', '');
        $('#pergunta_' + pergunta_id).find('.nova_opcao_container').hide();
        $('#pergunta_' + pergunta_id).find('.results_container').hide();
        $('#pergunta_' + pergunta_id).find('.pergunta_container').show();
    });
    
    $('.priorize_resultados').live('click', function() {
        var pergunta_id = $(this).parents('.priorize_pergunta').attr('id').replace('pergunta_', '');
        $('#pergunta_' + pergunta_id).find('.pergunta_container').hide();
        $('#pergunta_' + pergunta_id).find('.results_container').show();
    });
    
    $('.priorize_opiniao:not([href])').live('click', function() {
        var pergunta_id = $(this).parents('.priorize_pergunta').attr('id').replace('pergunta_', '');
        $('#pergunta_' + pergunta_id).find('.pergunta_container').hide();
        $('#pergunta_' + pergunta_id).find('.nova_opcao_container').show();
    });
    
    function priorize_loading(pergunta_id) {
        $('#pergunta_' + pergunta_id).find('.priorize_loading').show();
    }
    
    function priorize_hideLoading(pergunta_id) {
        $('#pergunta_' + pergunta_id).find('.priorize_loading').hide();
    }

    function priorize_show_message( pergunta_id, class_message, message ) {

        $('#pergunta_' + pergunta_id).find('.pergunta_container').addClass("message_active");

        $('#pergunta_' + pergunta_id).find( class_message ).html( message )
        .show()
        .delay(3000)
        .fadeOut( "slow", function(){
            $('#pergunta_' + pergunta_id).find('.pergunta_container').removeClass("message_active");
        });


    }

    function mb_strlen( str )
    {
        var len = 0;

        for( var i = 0; i < str.length; i++ ) {
            len += str.charCodeAt( i ) < 0 || str.charCodeAt( i ) > 255 ? 2 : 1;
        }

        return len;
    }

    // limita a quantidade de caracteres nos comentários
    $( '.priorize_nova_opcao_text' ).each( function() {
        var limit       = 140;
        var text        = $( this ).val();
        var text_length = mb_strlen( text );

        $( this ).after( '<div class="limit-chars-counter">( ' + ( limit - text_length ) + ' )</div>' );

        $( this ).keyup( function() {
            var text        = $( this ).val();
            var text_length = mb_strlen( text );

            if( text_length > limit )
            {
                $( this ).siblings( '.limit-chars-counter' ).html( '(<strong style="color:#AE2020;">' + limit + '</strong>)' );
                $( this ).val( text.substr( 0, limit ) );

                return false;
            }
            else
            {
                $( this ).siblings( '.limit-chars-counter' ).html( '( ' + ( limit - text_length ) + ' )' );

                return true;
            }
        } );
    } );

    $('.cronometro .count').addClass('counter-analog3').counter({
        initial: '0:00.0',
        direction: 'up',
        interval: '100',
        format: '99'
    }); 

         
    
});
