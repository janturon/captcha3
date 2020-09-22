# Start using captcha quickly

## Install and Requirements
 1. Set Captcha API key and secret for your web in Google console
 2. Define them in the prepared place in captcha3.php
 3. Enable CURL and HTTPS on your server
 4. Test the example below
 5. Read more [about ReCaptcha v3](https://developers.google.com/recaptcha/docs/v3) to understand the key concepts
 6. Adjust captchaText and captchaBadge functions to fit your design (it uses CURL, XHR and Promises)

## Example
```
<?
include "captcha3.php";
if(captchaText(@$_POST["captcha"])) echo "$_POST[login] is not a bot";
?>

<form method="POST">
  <input type="hidden" name="captcha">
  <input name="login">
  <input type="submit">
<form>

<?=captchaBadge()?>
```