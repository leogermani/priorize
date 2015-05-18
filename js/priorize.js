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
                
                priorize_show_success(pergunta_id);
                priorize_hideLoading(pergunta_id);
                
            });
    });
    
    $('.priorize_enviar_nova_opcao').live('click', function() {
        var pergunta_id = $(this).parents('.priorize_pergunta').attr('id').replace('pergunta_', '');
        var nova_opcao = $('#pergunta_' + pergunta_id).find('.priorize_nova_opcao_text').val();
        if (nova_opcao == '') {
            alert('Instira um texto para sua sugestão');
        }
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

                priorize_show_success(pergunta_id);
                priorize_hideLoading(pergunta_id);
                
            });
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
    
    $('.priorize_opiniao').live('click', function() {
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

    function priorize_show_success( pergunta_id ) {
        $('#pergunta_' + pergunta_id).find('.priorize_sucesso').show().delay(3000).fadeOut();
    }

    
});
