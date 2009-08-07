<? 
class Recorrencia {
	var $tipo; //o:Ocasional ou m:Mensal

	//Para recorrencia ocasional
	var $diasIntervalo;
	var $nOcorrencias;
	
	//Para recorrencia mensal
	var $diasSemana;
	var $diasMes;
	var $eou;
	
	function __construct($tipoR = "", $vars = "") {
		if (($tipoR == "") || ($vars=="")) return $this;
		$vars = explode(" ", $vars);
		
		switch ($tipoR) {
			case "0":	//OCASIONAL SEM Nº DE OCORRENCIAS
						$this->tipo = 'o';
						$this->diasIntervalo = $vars[0]*1;
						$this->nOcorrencias  = 0;
						break;
						
			case "1":	//OCASIONAL COM Nº DE OCORRENCIAS
						$this->tipo = 'o';
						$this->diasIntervalo = $vars[0]*1;
						$this->nOcorrencias  = $vars[1]*1;
						break;
						
			case "3":	//SEMANAL / MENSAL
						$this->tipo = 'm';
						$this->diasSemana = $vars[0];
						$this->diasMes  = $vars[1];
						$this->eou = $vars[2];	//acho que 1:e 0:ou
						break;						
		}		
	}
	
	function getTipoTexto() {
		switch ($this->tipo) {
			case 'o':	return "Ocasional"; break;
			case 'm':	return "Semanal/Mensal"; break;
			default :	return "Unknown";
		}
	}
	
	function getOutputLine1() {
		switch ($this->tipo) {
			case 'o':	return "Dias de Intervalo: ".$this->diasIntervalo; break;
			case 'm':	return "Dias da Semana: ".diasSemana2Human($this->diasSemana)." ".($this->eou?"e":"ou"); break;
			default :	return "Unknown";
		}
	}

	function getOutputLine2() {
		switch ($this->tipo) {
			case 'o':	if ($this->nOcorrencias) return "Número de Ocorrências: ".$this->nOcorrencias; break;
			case 'm':	return "Dias do Mês: ".diasMes2Human($this->diasMes); break;
			default :	return "Unknown";
		}
	}
	
	function getRecorrenciaForFile() {
		$out = array();
		switch ($this->tipo) {
			case 'o':	if (($this->nOcorrencias*1) != 0)	$out[0]=1;
						else $out[0]=0;
						$out[1] = sprintf("%06s %06s", $this->diasIntervalo,$this->nOcorrencias);
						break;
						
			case 'm':	$out[0]=3;
						$out[1] = $this->diasSemana." ".$this->diasMes." ".$this->eou;
						break;
		}
		return $out;
	}	
	
	function isSetDiaMes($d) {
		return $this->diasMes[$d];
	}
	
	function isSetDiaSemana($d) {
		return $this->diasSemana[$d];
	}
	
	function setFromWeb($t) {
		foreach($t as $k => $v) {
			$this->$k = $v;
		}
		$this->diasIntervalo *= 1;
		$this->nOcorrencias  *= 1;
	}
}	

?>
