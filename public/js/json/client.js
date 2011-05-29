$(document).ready(function(){
    api = jQuery.Zend.jsonrpc({url: '/json/server'});
   
    //alert('lista = ' + api.listarTodas());
    lista = api.listarTodas();

    $.each(lista, function(index, value) { 
         $('#palestras').append('<li>'+value+'</li>');
    });

 
    $('form').bind('submit',function(){
        //alert($('#chave').val());
        var msg ;
        pesquisa = api.pesquisar($('#chave').val());
        
        if(pesquisa != "")
        {
            msg = 'Achado curso:'+ pesquisa;
        }else{
            msg = 'Curso não encontrado';
        }

        $('#pesquisa').html(msg);
        return false;
    });
});


