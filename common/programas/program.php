<?
/*//Página requer que o utilizador se tenha autenticado
session_start();
include("users.php");
check_logged();
*/

global $client;
//e' necessario que tenha sido definida a var $params, para saber de qual jardim se vai obter as informacoes
global $params;

if(!isset($params) || !is_integer($params*1)) { ?>
	<div class="content"><strong>É necessário especificar o jardim. Volte ao mapa e seleccione um jardim.</strong></div>
<? 	return;
}

$j = $params*1; //$j é o jardim.

//temos o numero do jardim, precisamos de verificar se o user pode aceder-lhe
if (!hasPermission("j".$params) && !hasPermission("j*")) { ?>
	<div class="content"><strong>Jardim Inválido. Volte ao mapa e seleccione um jardim.</strong></div>
<? 	return;
}

//Apartir deste ponto podemos carregar a informação do jardim especifico.
require_once("../common/DBconnect.php");
$q = "SELECT name FROM jardins WHERE client like '%$client%' AND id = '$j'";
$res = mysql_query($q) or die(mysql_error());
$r = mysql_fetch_row($res);
$nomeJardim = $r[0];

?>
	<link rel="stylesheet" type="text/css" href="../common/css/programas.css" />
	<script src="../common/js/basicFunctions.js" type="text/javascript"></script>
	<script src="../common/js/JSON.js" type="text/javascript"></script>
	<script src="../common/js/i18n/ui.datepicker-pt-BR.js" type="text/javascript"></script>
	<script type="text/javascript" src="../common/programas/programas.js"></script>

	<script>var JARDIM=<? echo $j;?></script> 
	
<div id="nome-jardim" class="header-over-content">
	Programas do Jardim: <? echo utf8_encode($nomeJardim);?>
</div>
	
<div id="listaProgramas" class="sidebar-left">
	<ul>
	</ul>
<? if (hasPermission("edit_program")): ?>
	<div id="novoPrograma"><a href="javascript:novoPrograma();">Novo Programa</a></div>
<? endif; ?>
</div>

<div class="content with-left-sidebar programs">
			
		<div id="placeholder">Seleccione um dos programas na lista à esquerda.</div>
				
		<div class="programa show" style="display:none">
			<div class="header">
				<div class="nome"></div>
				<div id="activo">O programa está <span></span>. <a style="font-weight: bold;"></a>.</div>
<? if (hasPermission("edit_program")): ?>
				<div id="buttons">
					<input type="button" class="changes" id="edit" title="Editar o programa" value="editar" onclick="loadEditPrograma(_id)"/>
				</div>
<? endif; ?>
			</div>				
			
			<div id="inner-col-left">

				<div class="mainSettings">
					<div class="arranques"><span>Arranques:</span>
						<ul>
						</ul>
					</div>
					
					<div class="accoes"><span>Tempos e Dispositivos</span>
						<ul>
						</ul>
					</div>
				</div>
				
				
				<div class="otherSettings">
					<div class="header">Programação Suplementar:</div>
					<div class="recorrencia">
						<span></span>
						<div></div>
					</div>
	
					<div class="limites">
						Programa decorre entre <? /*echo $this->dataInicio;?> e <? echo $this->dataFim;*/?>.
					</div>
					
					<div class="tipoRega"><span>Tipo de Rega:</span> <span class="text"></span>
					</div>
					
					<div class="tipoPrograma">
					</div>
					
					<div class="controle">
						<span>Controle:</span>
						<span class="text"></span>
					</div>
					
					<div class="cobertura">
						<span>Cobertura:</span>
						<span class="text"></span>
					</div>
					
					<div class="dotacao">
						<span>Dotação de cada Rega:</span> <span class="text"></span> mm
					</div>
					
					<div class="pMaxRega">
						<span>% máx Autom. Rega:</span> <span class="text"></span>%
					</div>
				</div>
			</div>
		</div>
		
		
		<div id="mapa" class="mapa" style="display:none">
			Mapa das Zonas deste Jardim
	    </div>
		
		
		<!-- EDIÇÃO -->
		
		<div class="programa edit" style="display:none">
			<div class="header">
				<div class="nome"><input type="text" name="nome" onchange="update(this);"></div>
				<div class="activo"><input type="checkbox" name="activo" id="activar" onchange="update(this);" /> <label for="activar">Activar programa</label></div>
				<div id="buttons">
					<input type="button" class="changes" id="delete" title="Apagar o programa" value="apagar" onclick="deleteProg();"/>&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" class="changes" id="save" title="Guardar as alterações ao programa" value="guardar" onclick="saveEdit();"/> 
					<input type="button" class="changes" id="cancel" title="Não guardar as alterações ao programa" value="cancelar" onclick="cancelEdit();"/>
				</div>
			</div>
			<div class="mainSettings">
				<div class="arranques"><span>Arranques:</span>
					<ul>
					</ul>
					<div class="newValue">Novo: 
						<form id="newArranque" onsubmit="return newArranque(this);">
							<input name="h" id="arranqueH" type="text" size="2" value="HH" class="time"
										onfocus="if(this.value == 'HH') this.value = ''" 
							/>:<input name="m" id="arranqueM" type="text" size="2" value="MM" class="time"
										onfocus="if(this.value == 'MM') this.value = ''" 
							/>:<input name="s" id="arranqueS" type="text" size="2" value="SS" class="time"
										onfocus="if(this.value == 'SS') this.value = ''" />
							<input type="submit" value="+" class="ui-icon ui-state-default ui-icon-circle-plus"/>
						</form>
					</div>
				</div>
				
				<div class="accoes"><span>Tempos e Dispositivos</span>
					<ul class="sortable">
					</ul>
					<div class="newValue">Novo: 
						<form id="newAccao" onsubmit="return newAccao(this);">
							<input name="m" id="accaoM" type="text" size="3" value="MM" onfocus="if(this.value == 'MM') this.value = ''" class="time"
						/>:<input name="s" id="accaoS" type="text" size="2" value="SS" onfocus="if(this.value == 'SS') this.value = ''" class="time"
						/>:<input name="sectores" id="accaoSectores" type="text" size="15" value="Sector+Sector..." onfocus="if(this.value == 'Sector+Sector...') this.value = ''"/>
							<input type="submit" value="+" class="ui-icon ui-state-default ui-icon-circle-plus"/>
						</form>
					</div>
				</div>
			</div>
						
			<div class="otherSettings">
				<div class="header">Programação Suplementar:</div>
			<div class="inner-col-left">
				<div class="recorrencia">
					<span>Recorrência </span>
					
					<label for="rOcasional">
						<input type="radio" name="recorrencia" value="ocasional" id="rOcasional" onclick="showRecOcasional();update(this);"/>
						Ocasional
					</label>

					<label for="rMensal">
						<input type="radio" name="recorrencia" value="mensal" id="rMensal" onclick="showRecMensal();update(this);"/>
						Semanal/Mensal
					</label>
					
					
					<div class="recMensal">Dias da Semana:
						<div class="semana">
							<input type="button" class="dia" name="diasSemana[0]" value="Segundas" onclick="updateDiaSemana(this);"/>
							<input type="button" class="dia" name="diasSemana[1]" value="Terças"   onclick="updateDiaSemana(this);"/>
							<input type="button" class="dia" name="diasSemana[2]" value="Quartas"  onclick="updateDiaSemana(this);"/>
							<input type="button" class="dia" name="diasSemana[3]" value="Quintas"  onclick="updateDiaSemana(this);"/>
							<input type="button" class="dia" name="diasSemana[4]" value="Sextas"   onclick="updateDiaSemana(this);"/>
							<input type="button" class="dia" name="diasSemana[5]" value="Sábados"  onclick="updateDiaSemana(this);"/>
							<input type="button" class="dia" name="diasSemana[6]" value="Domingos" onclick="updateDiaSemana(this);"/>
						</div>
						<div class="eou">
							<label for="eou_e"><input name="eou" value="1" type="radio" id="eou_e" onclick="update(this);"/> e</label>
							<label for="eou_ou"><input name="eou" value="0" type="radio" id="eou_ou" onclick="update(this);"/> ou</label>
						</div>
						Dias do Mês:
						<div class="mes">
							<? for ($i=0; $i<31; $i++): ?>
								<input type="button" class="dia" name="diasMes[<? echo $i;?>]" value="<? echo $i+1;?>" onclick="updateDiaMes(this);"/>
							<? endfor; ?>

						</div>
					</div>
					
					<div class="recOcasional">
						Dias de Intervalo: <input type="text" name="diasIntervalo" size="3" onchange="update(this);"/><br/>
						Número de Ocorrências: <input type="text" name="nOcorrencias" size="3" onchange="update(this);"/>
					</div>
				</div>
				
				<div class="limites">
					<label for="limitarP"><input type="checkbox" id="limitarP" name="limitado" value="1" onclick="update(this);"/> Limitar o Programa</label>
					<br/>
					<div>
						Programa decorre entre <input type="text" name="dataInicio" class="datePicker" size="10" onchange="update(this);"/> e 
											   <input type="text" name="dataFim" class="datePicker" size="10" onchange="update(this);"/>.
					</div>
				</div>
			</div>
			<div class="inner-col-right">
				
				<div class="tipoRega"><span>Tipo de Rega:</span> 
					<input type="radio" name="tipoRega" id="sequencial" value="s" onclick="update(this);"/> 
						<label for="sequencial">Sequencial</label>
					<input type="radio" name="tipoRega" id="rotativo" value="r" onclick="update(this);"/>
						<label for="rotativo">Rotativo em</label>
					<input type="text" size="2" name="rotativoMin" id="trmm" value="MM" class="time"
						onfocus="if(this.value == 'MM') this.value = '';" onchange="update(this);"
				 />:<input type="text" size="2" name="rotativoSeg" id="trss" value="SS" class="time"
				 		onfocus="if(this.value == 'SS') this.value = ''" onchange="update(this);"/>
				</div>

				<div class="tipoPrograma">Programa de: 
					<label for="pRega" ><input type="radio" name="programa" value="Rega" 	id="pRega"  onclick="update(this);"/> Rega</label>
					<label for="pGeada"><input type="radio" name="programa" value="Geada"	id="pGeada" onclick="update(this);"/> Geada</label>
					<label for="pNeb"  ><input type="radio" name="programa" value="Neb"		id="pNeb"   onclick="update(this);"/> Neblização</label>
					<? /*<label for="prg3"><input type="radio" name="programa" value="fonte"	 id="prg3" onclick="update(this);"/> Fonte</label>
					<label for="prg4"><input type="radio" name="programa" value="show"	 id="prg4" onclick="update(this);"/> Show</label>*/?>
				</div>
				
				<div class="controle">
					<span>Controle:</span>
					<label for="cG"><input type="radio" name="controle" id="cG" value="g" onclick="update(this);"/> Geral</label>
					<label for="cE"><input type="radio" name="controle" id="cE" value="e" onclick="update(this);"/> Especifico:</label>
					<input type="text" size="3" name="valorEspcf" id="valorEspcf" onfocus="$('#cE').attr('checked',1);" onchange="update(this);"/>% &nbsp;
					<label for="etr"><input type="radio" name="controle" id="etr" value="etr" onclick="update(this);"/> ETR</label>
				</div>
				
				<div class="cobertura">
					<span>Cobertura:</span>
					<div>
						<? for ($i=1; $i<=19; $i++): ?>
							<label for="T<? echo $i;?>">
								<input type="radio" name="cobertura" value="T<? echo $i;?>" id="T<? echo $i;?>" onclick="update(this);"/> T<? echo $i;?>
							</label>
						<? endfor; ?>
				</div>
				
				<div class="dotacao">
					<span>Dotação de cada Rega:</span> <input type="text" name="mm" size="4" onchange="update(this);"/> mm
				</div>
				
				<div class="pMaxRega">
					<span>% máx Autom. Rega:</span> <input type="text" name="maxRega" size="4" onchange="update(this);"/> %
				</div>
			</div>
		</div>
		</div>

		<div style="clear:all">&nbsp;</div>
	</div>
	
</div><!-- content -->