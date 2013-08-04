<?
/*********************/
/*                   */
/*  Version : 0.1.0  */
/*  Author  : XF     */
/*  Comment : 130804 */
/*                   */
/*********************/
class iconvert{
	public $plural = array(	'mile' => 'miles', 
							'yard' => 'yards', 
							'foot' => 'feet', 
							'inch' => 'inches', 
							'fath' => 'faths', 
							'furlong' => 'furlongs' 
							);
							
	public $rescomp = 0;
	public $flag = 1;
	
	public function write($fn,$content)
	{
	 $fp = @fopen($fn, "a+");
	 fwrite($fp, $content . "\n");
	 fclose($fp);
	}
	
	public function readline($fn,$n)
	{
	 $a = file($fn);
	 return $a[$n];
	}
	
	public function lcov($length, $unit, $rules){
		$u = trim($unit);
		foreach ($this->plural as $k =>$v) {
			if (trim($v) ==$u) 
			{
				$u = $k;
				break;
			}
		}
		print "<br>lcov:" . $length . "*" . $rules[$u] . " = " . ($length * $rules[$u]);
		return $length * $rules[$u];
	}
	
	public function getrule($fn, $begin, $end){
		$strs = file($fn);
		for ($i = $begin; $i <= $end; $i++){
			$arule = split(" ", $strs[$i]);
			$unit = $arule[1];
			$length = ((float) $arule[3] / (float)$arule[0]);
			$rule[$unit] = $length;
		}
		return $rule;
	}
	
	public function compute($arr)
  	{
		$ariths = $arr['$ariths'];
		$res = $arr['res'];
		$rules = $arr['rules'];
		$flag = $arr['flag'];
		if ($flag){
			$this->compute($arr);
		} else {
			if (!empty($ariths[2]) && isset($ariths[2]))
			{				
				if ($ariths[2] == '+')
				{
					$res += $this->lcov($ariths[3], $ariths[4], $rules);
				}
				else
				{
					$res -= $this->lcov($ariths[3], $ariths[4], $rules);
				}
				
				print_r($ariths);
				for ($i = 0; $i < count($ariths) - 3; $i++)
				{
					$ariths[$i] = $ariths[$i+3];
				}				
				array_pop($ariths);
				array_pop($ariths);
				array_pop($ariths);
				print_r($ariths);
				$flag = 1;
			}
			else
			{
				$res = $this->lcov($ariths[0], $ariths[1], $rules);
				$flag = 0;
			}
		}
		print "<br>compute:" . implode("", $ariths) . "=". $res ;
		$re['ariths'] = $ariths;
		$re['res'] = $res;
		$re['rules'] = $rules;
		$re['flag'] = $flag;
		return $re;
  	}
	
	public function apop($arr, $n){
		print_r($arr);
		//$ar = array();
		//for ($i = 0; $i < count($arr) - $n; $i++)
		//	$ar[$i] = $arr[$i+$n];

		for ($i = 0; $i < $n; $i++)				
			array_pop($arr);
		return $arr;

	}

	public function computebak($ariths, $rules){
		$flag = 1;		
		while ($flag == 1) {
			$res = $this->rescomp;
			if (empty($ariths[2])){
				$res += $this->lcov($ariths[0], $ariths[1], $rules);
				$flag = 0;
				$this->rescomp = 0;	
				print "<br>" .$ariths[0].$ariths[1]."=". $res;
			} else {
				if ($ariths[2] == '+')
				{
					$res += $this->lcov($ariths[3], $ariths[4], $rules);
				}
				else
				{
					$res -= $this->lcov($ariths[3], $ariths[4], $rules);
				}
				$this->rescomp = $res;
				$ariths = $this->apop($ariths, 3);
				//if (empty($ariths[2])) $flag = 0;
				print "<br>compute=" .implode("", $ariths)."=". $res;
			}
			
		} ; 
	
		//return $this->rescomp;
		return $res;
	}
  
	public function getresult($fn, $begin, $end, $rules, $round){
		$strs = file($fn);
		for ($i = $begin; $i <= $end; $i++){
			$ariths = split(" ", $strs[$i]);
			print "<br>ariths:" .implode("",$ariths);
			$this->flag = 1;
			$rep = $this->computebak($ariths, $rules);
			$results[$i] = round($rep, $round);
			print "<br>";
		}
		return $results;
	}
}

$mail = "xfstudio@qq.com";
$fi = "input.txt";
$fo = "output.txt";

$ic = new iconvert();
$rules = $ic->getrule($fi, 0, 5);
print_r($rules);

$ic->write($fo, $mail);
$ic->write($fo, "");
$results = $ic->getresult($fi, 7, 16, $rules, 2);
foreach($results as $k => $v) $ic->write($fo, $v . " m");
?>