$.datepicker.setDefaults($.datepicker.regional['pt-BR']);

	

function getKey(name) {
	a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	name = name.toLowerCase().replace(/ /g,"_");
	return strtr(name, a, b); 
}

/*** LISTA DE PROGRAMAS ***/

function getListaProgramas() {
	$.ajax({
		type: "POST",
		url: "actions.php",
		data: "action=getListaProgramas&garden="+JARDIM,
		dataType: "json",
		success: function(list, status){
			var element = $("#listaProgramas ul").html("");
				
			if (list.length == 0) {
				element.append("<p style='text-align:center'>Não existem Programas</p>");
			} else {
				for( var i in list ) {
					var activo = (list[i].charAt(0))*1;
					var nome = list[i].substr(1);
					var key = getKey(nome);
					var str = "<li id='"+key+"' "+(activo?"class='activo'":"")+"><a>"+nome+"</a></li>";
					element.append(str);
				}
				$("#listaProgramas li").click(function(){
					id = $(this).attr("id");
					ajaxGetPrograma(id);
				})
			}
		}
	})
}


/*** LISTA DE JARDINS ***/

function getListaJardins() {
	$.ajax({
		type: "POST",
		url: "jardins.php",
		dataType: "text",
		success: function(script, status){
			eval(script);
		}
	})
}


/*** MOSTRAR PROGRAMA ***/

function ajaxGetPrograma(id) {
	$.ajax({
		type: "POST",
		url: "actions.php",
		data: "action=getPrograma&garden="+JARDIM+"&id="+id,
		dataType: "json",
		success: function (prog) {loadPrograma(prog)},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			alert("Erro a obter o Programa.\n\n"+XMLHttpRequest.responseText);
		}
	});
}

function loadPrograma(prog) {
	_id = getKey(prog["nome"]);
	data = prog;
	fillHTML(data);
	if (first) {$("#placeholder").fadeOut(); first = false;}
	$(".programa.show").show();
	$(".programa.edit").hide();
	//setShowActions();
	$(".mapa").show().removeClass("inEdition");
	//setTimeout("map.checkResize();map.setCenter(center, 13);", 200);
	//colocaMarcadores();
};

function loadEditPrograma(id) {
	fillFields(data);
	editionMode();
	$(".programa.edit #delete").show();
	//colocaTodosMarcadores();
};

function editionMode() {
	$(".programa.show").hide();
	$(".programa.edit").show();
	setEditActions();
	$("#mapa").addClass("inEdition");
	//setTimeout("map.checkResize();map.setCenter(center, 13);", 200);
}

function novoPrograma() {
	if (first) {$("#placeholder").fadeOut(); first = false;}
	_id = "NOVO";
	data = {
		"nome":"",
		"maxRega":"0",
		"activo":"0",
		"programa":"",
		"controle":"g",
		"valorEspcf":"0",
		"cobertura":"T1",
		"mm":"0",
		"rotativo":"0",
		"tempoRot":"",
		"recorrencia":{"tipo":"o","diasIntervalo":"0","nOcorrencias":"0","diasSemana":"","diasMes":"","eou":""},
		"limitado":"0",
		"dataInicio":"",
		"dataFim":"",
		"arranques":[],
		"accoes":[]
	};

	//clearFields();
	fillFields(data);
	editionMode();
	$(".programa.edit .nome input").val("<Nome>").focus(function (){if($(this).val() == "<Nome>") $(this).val("");});
//	$(".programa.edit .recorrencia > div").hide();
//	$(".programa.edit .limites div").hide();
	$(".programa.edit #delete").hide();
//	colocaTodosMarcadores();
	//map.clearOverlays();
	$("#mapa").show();
}

function saveEdit() {
	//VALIDAR CAMPOS
	if (data["nome"]=="") {
		alert("Tem que preencher o nome do programa."); $(".programa.edit .nome input").focus(); return false; }
	if (data["arranques"].length == 0) {
		alert("Tem que introduzir pelo menos uma hora de arranque."); $(".programa.edit #arranqueH").focus(); return false; }
	if (data["accoes"].length == 0) {
		alert("Tem que introduzir pelo menos uma acção (Tempo & Dispositivo).");$(".programa.edit #accaoM").focus(); return false; }
	
	delete data["coords"];
	enc_data = (JSON.encode(data)).replace("+","£");
//	log_debug(enc_data);
	$.ajax({
		type: "POST",
		url: "actions.php",
		data: "action=setPrograma&garden="+JARDIM+"&id="+_id+"&data="+enc_data,
		dataType: "json",
		processData: true,
		success: function (prog) {
			flashMsg("Programa Guardado!");
			getListaProgramas();
			loadPrograma(prog);
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			alert("Erro a guardar o Programa.\n\n"+XMLHttpRequest.responseText);
		}
	});
}

function cancelEdit() {
	if (_id == "NOVO") {
		$(".programa").add($("#mapa")).fadeOut();
		$("#placeholder").fadeIn();
		first = true;		
	} else {
		ajaxGetPrograma(_id);
	}
}

function deleteProg() {
	var answer = confirm("Apagar este programa?");
	if (answer) {
		$.ajax({
			type: "POST",
			url: "actions.php",
			data: "action=apagarPrograma&garden="+JARDIM+"&id="+_id,
			success: function(msg, status){
				$(".programa").fadeOut();
				$("#placeholder").fadeIn();
				first = true;
				$("#listaProgramas #"+_id).slideUp();
				flashMsg("Programa apagado!");
				$("#mapa").hide();
			}
		})
	}
}

function flashMsg(txt) {
	$("#message").text(txt).fadeIn("fast").animate({opacity: 1.0},3000).slideUp("slow");
}


/*** APLICAR DADOS ***/

function clearFields() {
	$(".programa.edit .arranques ul").text("");
	$(".programa.edit .accoes ul").text("");
	$(".programa.edit input").attr("checked",0);
	$(".programa.edit input").removeClass("selected");
}

function fillHTML(p) {
	$(".programa.show .nome").text(p["nome"]);
	
	if (p["activo"]=='1') {
		$(".programa.show #activo span").text("activado").addClass("activo");
		$(".programa.show #activo a").text("Desactivar");
	} else {
		$(".programa.show #activo span").text("desactivado").removeClass("activo");
		$(".programa.show #activo a").text("Activar");
	}
	
	arranques = $(".programa.show .arranques ul");
	arranques.html("");
	for(var a in p["arranques"]) {
		arranques.append("<li>"+p["arranques"][a]+"</li>");
	}
	
	accoes = $(".programa.show .accoes ul");
	accoes.html("");
	for(var a in p["accoes"]) {
		accoes.append("<li><span>"+p["accoes"][a]["duracao"]+"</span> &ndash; <span>"+p["accoes"][a]["sectores"]+"</span></li>");
	}
	
	//RECORRENCIA
	if (p["recorrencia"]["tipo"]=="o") {
		$(".programa.show .recorrencia span").text("Recorrência Ocasional");
		$(".programa.show .recorrencia div").text("Dias de Intervalo: "+p["recorrencia"]["diasIntervalo"]);
		if ((p["recorrencia"]["nOcorrencias"]*1)>0) {
			$(".programa.show .recorrencia div").append("<br/>Nº de Ocorrências: "+p["recorrencia"]["nOcorrencias"]);
		}
	} else { 
		$(".programa.show .recorrencia span").text("Recorrência Semanal/Mensal");
		$(".programa.show .recorrencia div").text("Dias da Semana: ");

		var str = "";
		var arrDS = p["recorrencia"]["diasSemana"];
		for(var i=0; i<7; i++) {
			var j = (arrDS.charAt(i))*1;
			if (j) {
				switch(i) {
					case 0: str += "Segundas, "; break;
					case 1: str += "Terças, "; break;
					case 2: str += "Quartas, "; break;
					case 3: str += "Quintas, "; break;
					case 4: str += "Sextas, "; break;
					case 5: str += "Sábados, "; break;
					case 6: str += "Domingos, "; break;
				};
			}
		}
		$(".programa.show .recorrencia div").append(str.substr(0,str.length-2));

		str = "";
		var arrDM = p["recorrencia"]["diasMes"];
		for(i=0; i<31; i++) {
			var j = arrDM.charAt(i)*1
			if (j) {
				str += ((i*1)+1)+", ";
			}
		}
		$(".programa.show .recorrencia div").append(" "+(p["recorrencia"]["eou"]*1?"e":"ou")+"<br/>Dias do Mês: "+str.substr(0,str.length-2));
	} //fim recorrencia
	
	//LIMITADO
	if (p["limitado"] == "1") {
		$(".programa.show .limites").text("Programa decorre entre "+p["dataInicio"]+" e "+p["dataFim"]+".");
	} else $(".programa.show .limites").text("");
	
	
	//ROTATIVO
	if(p["rotativo"]=="1") {
		var min = p["tempoRot"].substr(0,p["tempoRot"].indexOf(":"));
		var sec = p["tempoRot"].substr(-2)*1;
		min = (min*1)?" "+min+" min":"";
		sec = (sec)?" "+sec+" segs":"";
		if (min && sec) t = " e"; else t = "";
		$(".programa.show .tipoRega .text").text("Rotativo com intervalo de"+min+t+sec+".");
	} else {
		$(".programa.show .tipoRega .text").text("Sequencial");
	}
	
	//TIPO PROGRAMA
	if (p["programa"] == "Neb") { tp="Neblização"; } else { tp = p["programa"]; }
	$(".programa.show .tipoPrograma").text("Programa de "+tp);
	
	//CONTROLE
	switch (p["controle"]) {
		case "e":	str = "Rega especifica - "+(p["valorEspcf"]*1)+"%";break;
		case "etr": str = "Rega por ETR";break;
		case "g":	str = "Rega geral";break;
	}
	$(".programa.show .controle span.text").text(str);
	
	//COBERTURA
	$(".programa.show .cobertura .text").text(p["cobertura"]);
	
	$(".programa.show .dotacao .text").text(p["mm"]);
	$(".programa.show .pMaxRega .text").text(p["maxRega"]);
}

function fillArranques(p) {
	$(".programa.edit .arranques ul").html("");
	for (var a in p["arranques"]) {
		$(".programa.edit .arranques ul").append("<li><div class='remove' id="+a+"/> "+p["arranques"][a]+"</li>");
	}
	$(".arranques .remove").click(function () {
		var id = $(this).attr("id")*1;
		data["arranques"].splice(id,1);
		fillArranques(data);
	})
}

function fillAccoes(p) {
	$(".programa.edit .accoes ul").html("");
	for (var a in p["accoes"]) {
		$(".programa.edit .accoes ul").append("<li id="+a+"><span class='vertical-arrow'></span><div class='remove' id="+a+" src='remove.png'/><span>"+p["accoes"][a]["duracao"]+"</span> &ndash; <span>"+p["accoes"][a]["sectores"]+"</span></li>");
	}
	$(".accoes .remove").click(function () {
		var id = $(this).attr("id")*1;
		data["accoes"].splice(id,1);
		fillAccoes(data);
	})
	$(".sortable").sortable({placeholder: 'highlight', update: function () {updatePosicoesAccoes();} });
	$(".sortable").disableSelection();
	//colocaMarcadores();

}
function fillFields(p) {
	clearFields();
	$(".programa.edit .nome input").val(p["nome"]);

	$(".programa.edit #activar").attr("checked",p["activo"]*1);

	fillArranques(p);
	fillAccoes(p);
	
	if(p["recorrencia"]["tipo"] == "o") {
		$(".recorrencia #rOcasional").attr("checked",1);
		$(".recorrencia .recMensal").hide();
		$(".recorrencia .recOcasional").show();
		$(".recorrencia .recOcasional input[name=diasIntervalo]").val(p["recorrencia"]["diasIntervalo"]);
		$(".recorrencia .recOcasional input[name=nOcorrencias]").val(p["recorrencia"]["nOcorrencias"]);
	} else {
		$(".recorrencia #rMensal").attr("checked",1);
		$(".recorrencia .recOcasional").hide();
		$(".recorrencia .recMensal").show();
		$(".recorrencia #eou_"+(p["recorrencia"]["eou"]*1?"e":"ou")).attr("checked",1);
		for(var i=0; i<7; i++) {
			if (p["recorrencia"]["diasSemana"].charAt(i)*1) {
				$(".recMensal .semana .dia[name=diasSemana["+i+"]]").addClass("selected");
			}
		}
		for(i=0; i<31; i++) {
			if (p["recorrencia"]["diasMes"].charAt(i)*1) {
				$(".recMensal .mes .dia[name=diasMes["+i+"]]").addClass("selected");
			}
		}
	}
	
	if(p["limitado"]*1) {	
		$(".limites #limitarP").attr("checked",1);
		$(".limites input[name=dataInicio]").val(p["dataInicio"]);
		$(".limites input[name=dataFim]").val(p["dataFim"]);
		$(".limites div").show();
	} else {
		$(".limites #limitarP").attr("checked",0);
		$(".limites div").hide();					
	}


	if(p["rotativo"]=="1") {
		$(".tipoRega #rotativo").attr("checked",1);
		var split = p["tempoRot"].indexOf(":");
		var min = p["tempoRot"].substr(0,split);
		var sec = p["tempoRot"].substr(split+1);
		$(".tipoRega #trmm").val(min);
		$(".tipoRega #trss").val(sec);
		$(".tipoRega input[type=text]").attr("disabled",0);
	} else {
		$(".tipoRega #sequencial").attr("checked",1);
		$(".tipoRega input[type=text]").attr("disabled",1);
	}
	
	if(p["programa"]!="")
		$("input#p"+(p["programa"])).attr("checked",1);
	
	if(p["controle"]=="e") {
		$(".controle input#valE").val(p["valorEspcf"]);
		cID = "cE";
	}
	else if (p["controle"]=="g") {		cID = "cG";		}
	else if (p["controle"]=="etr") {	cID = "etr";	}
	$(".controle input#"+cID).attr("checked",1);
	
	if(p["cobertura"]!="")
		$(".cobertura input#"+p["cobertura"]).attr("checked",1);
	
	$(".dotacao input").val(p["mm"]);
	
	$(".pMaxRega input").val(p["maxRega"]);
}		

/*** SET ACTIONS ***/
function setShowActions() {
	$(".programa.show #activo a").click(function(){
		if (data["activo"]*1) { accao = "desactivarPrograma"; } else { accao = "activarPrograma"; }
		$.post("actions.php",
			{
				action: accao,
				id: _id,
				garden: JARDIM
			},
			function (prog) {
				loadPrograma(prog);
				updateItemListaProgramas();
	   		}, "json"
		);
	});
}


function setEditActions() {
	$(".datePicker").datepicker();
}


// MAP: ISTO JÁ NÃO EXISTE
/*
function colocaMarcadores() {
	map.clearOverlays();
	var sectores;
	var markersToDisplay = new Object();
	for (var a in data["accoes"]) { //obtem os sectores de cada accao
		sectores = data["accoes"][a]["sectores"].split(/[^\d]/);
		for (var i in sectores) {
			markersToDisplay[sectores[i]] = markers[sectores[i]]; //fazer push podia fazer um marcador aparecer várias vezes
		}
	}
	displayMarker(map, markersToDisplay, true);
}

function colocaTodosMarcadores() {
	$.get("jardins.php", function (data) {eval(data)});
}
*/

/*** UPDATE FUNCTIONS ***/

function showRecOcasional() {
	$(".recMensal").hide();
	if (navigator.appName=="Microsoft Internet Explorer") {
		$(".recOcasional").show();
	} else {
		$(".recOcasional").slideDown();
	}
}

function showRecMensal() {
	$(".recOcasional").hide();			
	if (navigator.appName=="Microsoft Internet Explorer") {
		$(".recMensal").show();
	} else {
		$(".recMensal").slideDown();
	}
}	

function updateItemListaProgramas() {
	if (data["activo"]*1) {
		$("#listaProgramas #"+_id).addClass("activo");
	} else {
		$("#listaProgramas #"+_id).removeClass("activo");
	}
}


function updateDiaSemana(obj) {
	$(obj).toggleClass("selected");
	var dia = obj.name.charAt(11)*1;
	var str = data["recorrencia"]["diasSemana"];
	if ((str == "") || (str==null)) str = "0000000";
	var char = ($(obj).hasClass("selected")?'1':'0');
	data["recorrencia"]["diasSemana"] = setCharAt(str,dia,char);
}

function updateDiaMes(obj) {
	$(obj).toggleClass("selected");
	var dia = (obj.name.substr(8,obj.name.indexOf(']')-8))*1;
	var str = data["recorrencia"]["diasMes"];
	if ((str == "") || (str == null)) str = "0000000000000000000000000000000";
	var char = ($(obj).hasClass("selected")?'1':'0');
	data["recorrencia"]["diasMes"] = setCharAt(str,dia*1,char);
}

/*function updatePrograma(obj) {
	var a = Array();
	$(".tipoPrograma input").each(function () {
		if(this.checked) {
			a.push(this.name.substr(9));
		}
	})
	data["programa"] = a;
}*/

function updatePosicoesAccoes() {
	var changes = $(".sortable").sortable('toArray');
	var newArray = Array();
	for(var i in changes) {
		newArray[i] = data["accoes"][(changes[i])*1];
	}
	data["accoes"]=newArray;
	fillAccoes(data);
}

function newArranque(form) {
	h = form["h"].value*1;
	m = form["m"].value*1;
	s = form["s"].value*1;
	
	if(isNaN(h) || isNaN(m) || isNaN(s) || (h>23 || h<0) || (m>59 || m<0) || (s>59 || s<0)) {
		alert("Por favor introduza uma hora válida.");
		return false;
	}
	
	data["arranques"].push(((h+'').length==1?"0"+h:h)+":"+((m+'').length==1?"0"+m:m)+":"+((s+'').length==1?"0"+s:s));
	fillArranques(data);
	return false;
}

function newAccao(form) {
	m = form["m"].value*1;
	s = form["s"].value*1;

	if(isNaN(m) || isNaN(s) || (m<0) || (s>59 || s<0)) {
		alert("Por favor introduza uma duração válida.");
		return false;
	}
	
	accao = new Object();
	accao["duracao"] = ((m+'').length==1?"0"+m:m)+":"+((s+'').length==1?"0"+s:s);
	accao["sectores"] = (form["sectores"].value);
	data["accoes"].push(accao);
	fillAccoes(data);
//	colocaMarcadores();
	return false; //para não mudar de página devido a estar a funcionar em cima de um form
}

function update(obj) {
	switch (obj.name) {
		case "recorrencia":	data["recorrencia"]["tipo"] = obj.value.substr(0,1); break;
		case "activo":		data[obj.name] = obj.checked?1:0; break;
		case "diasIntervalo":
		case "nOcorrencias":	data["recorrencia"][obj.name] = obj.value; break;
		case "tipoRega":		if(obj.value == "s") {$('.tipoRega input[type=text]').attr('disabled',1); data["rotativo"] = 0;}
								else {$('.tipoRega input[type=text]').attr('disabled',0);data["rotativo"] = 1;} break;						case "rotativoMin":
		case "rotativoSeg":		data["tempoRot"] = $("#trmm").val()+":"+$("#trss").val(); break;
		case "eou":				data["recorrencia"]["eou"] = obj.value; break;

		case "limitado":	if(obj.checked){$(".limites div").slideDown();data["limitado"]=1}else{$(".limites div").slideUp();data["limitado"]=0};break;
		case "controle":
		case "valorEspcf":
		case "programa":		
		case "nome":		
		case "dataInicio":	
		case "dataFim":	
		case "cobertura":	
		case "maxRega":		
		case "mm":			data[obj.name] = obj.value; break;
	}
	log_debug(print_r(data));
}

/* RUN *****************************************************************/
var data = Array();
var _id;
var programaVisivel;
var first = 1;

$(document).ready(function(){
	getListaProgramas();	
	setShowActions();
	setEditActions();	
})


function strtr (str, from, to) {
    // Translates characters in str using given translation tables  
    // 
    // version: 905.3122
    // discuss at: http://phpjs.org/functions/strtr
    // +   original by: Brett Zamir (http://brett-zamir.me)
    // +      input by: uestla
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Alan C
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: $trans = {'hello' : 'hi', 'hi' : 'hello'};
    // *     example 1: strtr('hi all, I said hello', $trans)
    // *     returns 1: 'hello all, I said hi'
    // *     example 2: strtr('äaabaåccasdeöoo', 'äåö','aao');
    // *     returns 2: 'aaabaaccasdeooo'
    // *     example 3: strtr('ääääääää', 'ä', 'a');
    // *     returns 3: 'aaaaaaaa'
    // *     example 4: strtr('http', 'pthxyz','xyzpth');
    // *     returns 4: 'zyyx'
    // *     example 5: strtr('zyyx', 'pthxyz','xyzpth');
    // *     returns 5: 'http'
    var fr = '', i = 0, j = 0, lenStr = 0, lenFrom = 0;
    var tmpFrom = [];
    var tmpTo   = [];
    var ret = '';
    var match = false;

    // Received replace_pairs?
    // Convert to normal from->to chars
    if (typeof from === 'object') {
        for (fr in from) {
            tmpFrom.push(fr);
            tmpTo.push(from[fr]);
        }

        from = tmpFrom;
        to   = tmpTo;
    }
    
    // Walk through subject and replace chars when needed
    lenStr  = str.length;
    lenFrom = from.length;
    for (i = 0; i < lenStr; i++) {
        match = false;
        for (j = 0; j < lenFrom; j++) {
            if (str.substr(i, from[j].length) == from[j]) {
                match = true;

                // Fast forward
                i = (i + from[j].length)-1;
                
                break;
            }
        }
        
        if (false !== match) {
            ret += to[j];
        } else {
            ret += str[i];
        }
    }

    return ret;
}
