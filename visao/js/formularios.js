$(function(){
    var projecto = '/SGPA/Controlo/';
    debugar =  $('.mensagem');
    action  =  projecto+'run.php';
    botao   =  $('.enviar');

    botao.attr("type","submit");
    function caregando(){
        debugar.empty().css({font: '19pt "Arial", cursive' }).html('<div style="background: #1a76bf; color: #fff; padding: 5px; text-align:center;"><center><img  src="'+projecto+'application/public/arquivo/imagem/loader.gif" style="width:35px; height:35px;"> processando...</center></div>');   
    }
    function resposta(dados){
        debugar.empty().css({font: '19pt "Arial", cursive' }).html(dados);   
    }
    
    function erro(){
        debugar.empty().css({font: '12pt "Arial", cursive' }).html('<div style="background: red; color: #fff;  padding: 5px; text-align:center;"><center>Erro ao enviar dados!!!</center></div>'); 
        
    }

    function concluido(dados){
        location.href=""+dados; 
    }

   
    $.ajaxSetup({
        url:            action, 
        type:           'POST',
        data:           $(this).serialize(),
        beforeSend:     caregando,
        error:          erro,
    });

    formSend = $('form[name="formSend"]');
    formSend.submit(function(){
        $.ajax({  
            data:    $(this).serialize(),
            success: function(resultado){
                resposta(resultado);
            }
        });
        return false;  
    });

   /** 
    enviar.submit(function(){
        var ViaGet = $.get(logim, $(this).serialize());
        ViaGet.progress( resposta('<img src="imagens/Aguardar.gif" width="100">'));
        ViaGet.done(resposta);
        ViaGet.fail(function(){
            resposta('Erro!!');
        });
        return false;
    });
    **/

    jQuery(function(){ 
    	debugar =  $('.mensagem');
    	jQuery('body').on('click', '.close', function(){
            debugar.empty();
        });
    });

    $(document).ready(function(){
    	DataHoje();
    });

    function DataHoje(){ 
    	var today = new Date();
    	var h=today.getHours();			
    	var m=today.getMinutes();			
    	var s=today.getSeconds();				
    	m=checkTime(m);			
    	s=checkTime(s);
    	document.getElementById('hora').innerHTML=h+":"+m+":"+s;			 
    	t=setTimeout("DataHoje()",500);      
    }	

    function checkTime(i){ 
    	 if (i<10){			
    		 i="0" + i;
    	 }			
    	return i;
    }

    var qrcode = new QRCode(document.getElementById("qrcode"), {
        width : 100,
        height : 100
    });

    function makeCode () {      
        var elText = document.getElementById("textQR");
        
        if (!elText.value) {
            alert("Digite um Texto");
            elText.focus();
            return;
        }
        
        qrcode.makeCode(elText.value);
    }

    makeCode();

    $("#textQR").
        on("blur", function () {
            makeCode();
        }).
        on("keydown", function (e) {
            if (e.keyCode == 13) {
                makeCode();
            }
        });

        
    });

