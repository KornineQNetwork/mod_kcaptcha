<?php
class mod_kcaptcha extends ModuleHelper {
	private $kcaptcha;
	private $kaptcha_api = "https://sys.kornineq.de/kaptcha/kaptcha.php";
	private $sitekey = "";

	public function getModuleName(){
		return 'mod_kcaptcha : mod_kcaptcha';
	}

	public function getModuleVersionInfo(){
		return '1.0';
	}

	public function autoHookHead(&$head, $isReply){
		//nothing
	}

	public function autoHookPostForm(&$txt){
        $txt .= '<tr><th class="postblock">Verification</th><td>'.'<script src="https://sys.kornineq.de/kaptcha/kaptcha.js?536346s" data-sitekey="'.$this->KEY_PUBLIC.'" type="text/javascript"></script>'.'</td></tr>';
	}
	
	function kaptcha_validate($key) {
		$k = $_REQUEST["_KAPTCHA"]??false;
		if (!$k) return false;
		$opts = array(
			'http' => array(
				'method' => 'GET',
				'header' => 'X-Requested-Domain: ' . $_SERVER['HTTP_HOST'] . "\r\n"
			)
		);
		$context = stream_context_create($opts);
		$result = file_get_contents(
			$this->kaptcha_api."?_KAPTCHA=".$k."&key=".$key."&site=".$this->sitekey, // Add $this->
			false,
			$context
		);
		
		return stristr($result, "CHECK correct") ? 1 : 0;
	}

	function check_captcha() {
		if (isset($_POST["_KAPTCHA"]))
			return $this->kaptcha_validate($_POST["_KAPTCHA_KEY"]);
		return false;
	}

	public function autoHookRegistBegin(&$name, &$email, &$sub, &$com, $upfileInfo, $accessInfo){
		$resp = $this->check_captcha();
		if(!$this->check_captcha()){ die('You are not acting like a human!'); } 
	}
}
