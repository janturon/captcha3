<?php
// warning: HTTPS required

// get these values from Google console
// you must pair it with the target web (i.e. can't test it from localhost)
define("CAPTCHA_SECRET", "yourCaptchaSecret");
define("CAPTCHA_APIKEY", "yourCaptchaApiKey");

// call captchaTest($_POST["captcha"]) on the form handler server-side
// returns true if not bot
function captchaTest($captchaToken, $secret=CAPTCHA_SECRET) {
  $server = "https://www.google.com/recaptcha/api/siteverify";
  $post = [
    "secret" => $secret,
    "response" => $captchaToken
  ];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $server);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$answer = curl_exec($ch);
	curl_close($ch);
  $result = json_decode($answer);
  // optional: $result->score returns heuristics 0-1
  // 0 = 100% bot, 1 = 100% human
  // $result->success is boolean
  return $result->success;
}

// place this generated javascript to client-side
// place <input type="hidden" name="captcha"> into target form (the captcha token is generated here)
// call captchaBadge only once per site, it can serve multiple forms
function captchaBadge($apikey=CAPTCHA_APIKEY) {
return '
  <div id="recaptcha-badge"></div>
  <script>
    var clientId; // set by placeCaptcha

    // called by placeCaptcha after grecaptcha is provided by Google
    function getReCaptcha() {
      grecaptcha.ready(function() {
        grecaptcha.execute(clientId).then(function(token) {
          var captcha = document.getElementsByName("captcha");
          Array.from(captcha).forEach(el => el.value = token);
          document.querySelector("#recaptcha-badge iframe").tabIndex = -1;
        });
      });
    }

    // called by RECAPTCHA API, see below
    function placeCaptcha() {
      clientId = grecaptcha.render("recaptcha-badge", {
        "sitekey": "'.$apikey.'",
        "badge": "inline",
        "size": "invisible"
      });
      getReCaptcha();
      setInterval(getReCaptcha, 120000); // update token every 2 minutes
    }
  </script>
  <script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=placeCaptcha"></script>
';
}
