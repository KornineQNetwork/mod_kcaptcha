<?php
class mod_kcaptcha extends ModuleHelper {
	private $KEY_PUBLIC   = '78fd1ed67e4e25a7fd5258ff5af96196';
	private $kcaptcha;
	private $kaptcha_api = "https://sys.kornineq.de/kaptcha/kaptcha-verify.php";
	private $sitekey = "";
	private $sitesecret = "";

	public function getModuleName(){
		return 'mod_kcaptcha : mod_kcaptcha';
	}

	public function getModuleVersionInfo(){
		return '1.1';
	}

	public function autoHookHead(&$head, $isReply){
		//nothing
	}

	public function autoHookPostForm(&$txt){
        $txt .= '<tr><th class="postblock">Verification</th><td>'.'<script src="https://sys.kornineq.de/kaptcha/kaptcha.js?536346s" data-sitekey="'.$this->KEY_PUBLIC.'" type="text/javascript"></script>'.'</td></tr>';
	}
	
	function kaptcha_validate() {
	    if(!isset($_POST["_KAPTCHA"])) { return false; }
	   
	    $postFields = [
	        'key' => $_POST["_KAPTCHA_KEY"],
	        'site_key' => $this->sitekey,
	        'site_secret' => $this->sitesecret
	    ];
   
	    $ch = curl_init($this->kaptcha_api);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
	    $result = curl_exec($ch);
	    curl_close($ch);
    
	    $response = json_decode($result, true);
   
	    return isset($response['success']) && $response['success'] === true;
	}
	
	function check_captcha() {
		if (isset($_POST["_KAPTCHA"]))
			return $this->kaptcha_validate();
		return false;
	}

	public function autoHookRegistBegin(&$name, &$email, &$sub, &$com, $upfileInfo, $accessInfo){
		if(!$this->check_captcha()){ die('You are not acting like a human!'); } 
	}
}
