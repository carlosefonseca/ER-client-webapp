<?

require_once("accao.class.php");
require_once("recorrencia.class.php");

class Programa {
	var $nome;
	var $maxRega;
	var $activo;
	var $programa;
	var $controle;
	var $cobertura;
	var $valorEspcf;
	var $mm;
	var $rotativo;
	var $tempoRot;
	var $recorrencia;
	var $progInicio;
	var $progFim;
	var $limitado;
	var $dataInicio;
	var $dataFim;
	
	var $arranques;
	var $accoes;
	
	
	function initFromFile($t) {
		//Divide um programa nas várias linhas
		$prog_array = explode("\n", $t);
//		echo "\nPA: ".$prog_array[0]."|".strpos($prog_array[0], "InicioDePrograma")."\n";
		
		//Coloca as informações do programa nos campos adequados no array
		$this->nome = trim($prog_array[0]);
		$this->maxRega = $prog_array[1];
		
		list(	$this->activo,
				$tempProgCobertura,
				$this->controle, // É O TIPO DE CONTROLE  0: rega especifica; 1: rega por ETR; 3: rega geral	
				$this->valorEspcf,
				$this->mm,
				$this->rotativo,
				$this->tempoRot,
				$tempRecorrencia, //0-sem ocorrencias | 1-com ocorrencias | 3-mensal
				$this->progInicio,
				$this->progFim,
				$this->limitado,
				$this->dataInicio,
				$this->dataFim,
				$tempRecorrenciaVars) = preg_split("/[ \n]/", $prog_array[2], 14);
//				echo "-> ".$this->nome." > ".$prog_array[2];
				
				//die($prog_array[2]."\n\n\n\n".print_r($aaa,true));
		
		$this->cobertura = substr($tempProgCobertura, 0,2)+1;
		$this->programa = substr($tempProgCobertura, -1);
		$this->recorrencia = new Recorrencia($tempRecorrencia, $tempRecorrenciaVars);
		$this->mm *= 1;
		$this->maxRega *= 1;
		$this->tempoRot *= 1;
		
		if (trim($prog_array[3])!="") {
			$this->arranques = explode(" ", $prog_array[3]);
		} else {
			$this->arranques = array();
		}
		
		//Array com pares (tempo, sectores)
		$accoes = array();
		for ($i = 4; (strpos($prog_array[$i], "FimDePrograma") === false) && ($i < count($prog_array)) ; $i++) {
			$accao = new Accao();
			list($accao->duracao, $accao->sectores) = preg_split("/,/", $prog_array[$i], 2);
			$accoes[] = $accao;
		}

		//Junta o array de sectores ao programa
		$this->accoes = $accoes;
	}
	
	function outputForFile() {
		$out = "InicioDePrograma\r\n";

		$r = $this->recorrencia->getRecorrenciaForFile();
				
		$out .= sprintf("%s\r\n%04s\r\n%s %02s%s %s %03s %03s %s %06s %s %06s %06s %s %s %s %s\r\n",
			$this->nome,
			$this->maxRega,
			$this->activo, //linha grande
			$this->cobertura-1,
			$this->programa,
		    $this->controle,
		    $this->valorEspcf,
		    $this->mm,
		    $this->rotativo,
		    $this->tempoRot,
		    $r[0],
		    date2daysSince($this->dataInicio),
		    date2daysSince($this->dataFim),
		    $this->limitado,
		    $this->dataInicio,
		    $this->dataFim,
		    $r[1]);
			
		$out .= implode(" ", $this->arranques)."\r\n";
		
		foreach ($this->accoes as $s) {
			$out .= $s->duracao.",".$s->sectores."\r\n";
		}
		
		$out .= "FimDePrograma\r\n";
		return $out;
	}
	
	function getTipoDeRega() {
		if ($this->rotativo) {
			$tempo = sec2time($this->tempoRot*1);
			return "Rotativo com intervalo de ".($tempo["minutes"]?$tempo["minutes"]." min ":"").($tempo["seconds"]?$tempo["seconds"]." segs":"");
		} else {
			return "Sequencial";
		}
	}
		
	function getProg() { 
		switch ($this->programa) {
			case 1:  return "Rega";
			case 2:  return "Geada";
			case 9:  return "Neb";
/*			case 3:  return "Fonte";
			case 4:  return "Show"; */
			default: return "";

		}
	}

	function setProg($p) { 
		switch ($p) {
			case "Rega":  $this->programa = "1"; break;
			case "Geada": $this->programa = "2"; break;
			case "Neb":	  $this->programa = "9"; break;
/*			case "Fonte": $this->programa = 
			case "Show":  $this->programa = */
			default: 	  $this->programa = "0"; break;
		}
	}
	
	function getCobertura() {
		return "T".($this->cobertura);
	}
	
	function getControle() {
		switch(($this->controle)*1) {
			case 0: return "e";	// Especifica
			case 1: return "etr";	// ETR
			case 3: return "g";	// Geral
		}
	}

	function setControle($v) {
		switch($v) {
			case "e": $this->controle = 0; break;	// Especifica
			case "etr": $this->controle = 1; break;	// ETR
			case "g": $this->controle = 3; break;	// Geral
		}
	}
	
	function getDotacaoRega() {
		return ($this->mm*1)." mm";
	}
	
	function getPMaxRega() {
		return ($this->maxRega*1)."%";
	}
	
	function getKey() {
		return str_replace(" ", "_", trim(normaliza($this->nome)));
	}
	
	function activar() 		{	$this->activo = 1; 	}
	function desactivar()	{	$this->activo = 0;	}
	
	function getReadableArranques() {
		$out = array();
		if (count($this->arranques)!=0) {
			foreach($this->arranques as $a) {
				$tempo = sec2time($a*1);
				$out[] = sprintf("%02d:%02d:%02d", $tempo["hours"], $tempo["minutes"], $tempo["seconds"]);
			}
		}
		return $out;
	}
	
	function getReadableAccoes() {
		$out = array();
		foreach($this->accoes as $a) {
			$out[] = $a->getReadableArray();
		}
		return $out;
	}
	
	function getTempoRot() {
		$tempo = sec2time($this->tempoRot);
		return sprintf("%02d:%02d", $tempo["minutes"],$tempo["seconds"]);
	}
	
	function getAllCoords() {	
		global $client;	
		$q = "SELECT * FROM jardins WHERE client LIKE '$client'";
		$res = mysql_query($q) or die(mysql_error());
		$array = array();
		
		if(mysql_num_rows($res)!=0) {
			
/*			$max_lat = $max_lng = -9999;
			$min_lat = $min_lng =  9999;
*/			
			while ($r = mysql_fetch_array($res)) {
				array_push($array, $r);
			}
/*				echo "createMarkerAndPoint(map, ".$r['lat'].",".$r['lng'].", '".$r['id']."', '".$r['name']."', '".$r['status']."', '". processMarkerData($r['data'])."');\n";
				if ($r['lat'] && $r['lng']) {
		    		if ($r['lat'] < $min_lat) { $min_lat = $r['lat']; }
		    		if ($r['lat'] > $max_lat) { $max_lat = $r['lat']; }
		    		if ($r['lng'] < $min_lng) { $min_lng = $r['lng']; }
		    		if ($r['lng'] > $max_lng) { $max_lng = $r['lng']; }
		    	}
			}
			
			//echo "// $max_lat $min_lat\n//$max_lng $min_lng";
			$c_lat = ($max_lat + $min_lat)/2;
			$c_lng = ($max_lng + $min_lng)/2;
			
/*			if ($c_lat || $c_lng) {
				echo "center = new GLatLng('".str_replace(",", ".", $c_lat)."' , '".str_replace(",", ".", $c_lng)."');";
				?>
				map.setCenter(center);
				map.savePosition();
		<?	}	*/
		}
//		print_r($array);
		return $array;
	}
	
	function toBrowser() {
		$out = array();
		$out["nome"]=$this->nome;
		$out["maxRega"]=$this->maxRega;
		$out["activo"]=$this->activo;
		$out["programa"]=$this->getProg();
		$out["controle"]=$this->getControle();
		$out["cobertura"]=$this->getCobertura();
		$out["valorEspcf"]=$this->valorEspcf;
		$out["mm"]=$this->mm;
		$out["rotativo"]=$this->rotativo;
		$out["tempoRot"]=$this->getTempoRot();
		$out["recorrencia"]=$this->recorrencia;
		$out["limitado"]=$this->limitado;
		$out["dataInicio"]=$this->dataInicio;
		$out["dataFim"]=$this->dataFim;
		$out["arranques"]=$this->getReadableArranques();
		$out["accoes"]=$this->getReadableAccoes();
//		$out["coords"]=$this->getAllCoords();
		return $out;
	}
		
	function updateFromSite($data) {
$DEBUG=0;
if($DEBUG) { // DEBUG
		echo "<table><tr><td>";
		echo "PREVIOUS";
		echo "<pre>".print_r($this,true)."</pre>";
}	
		$data = (Array)$data;

		if(!$data['nome']) {die("Nome do Programa necessário!");}
	
		$this->nome = str_replace("+", " ",$data['nome']);
		$this->maxRega = $data['maxRega'];
		$this->activo = $data['activo'];
		$this->valorEspcf = $data['valorEspcf'];
		$this->mm = $data['mm'];
		$this->rotativo = $data['rotativo'];
		$this->tempoRot = time2sec($data['tempoRot']);
		$this->limitado = $data['limitado']*1;
		$this->dataInicio = ($data['dataInicio']!=""?$data['dataInicio']:"00/00/0000");
		$this->dataFim = ($data['dataFim']!=""?$data['dataFim']:"00/00/0000");
		$this->cobertura = substr($data["cobertura"], 1);
		$this->arranques = arrayTime2Sec($data["arranques"]);

		//Estes definem a var sozinhos
		$this->recorrencia->setFromWeb($data["recorrencia"]);
		$this->setProg($data["programa"]);
		$this->setControle($data["controle"]);

//		echo "[".print_r($data['accoes'],true)."]";

		$this->accoes = array();
		foreach($data["accoes"] as $k => $v) {
			$a = new Accao();
			$a->setFromWeb($data["accoes"][$k]);
			$this->accoes[] = $a;
		}
		
if($DEBUG) { // DEBUG
		echo "</td><td>";
		echo "OUT:";
		echo "<pre>".print_r($this,true)."</pre>";
		echo "</td></table>";
}
	}
}

?>