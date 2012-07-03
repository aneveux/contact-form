<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Contact Form</title>
<meta name="description" content="PHP, HTML5, CSS3, jQuery contact form">
<meta name="author" content="Antoine Neveux">
<link type="text/plain" rel="author" href="./humans.txt" />

<meta http-equiv="content-language" content="en">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1.0">

<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
<link
	href='http://fonts.googleapis.com/css?family=Questrial|Droid+Sans|Alice'
	rel='stylesheet' type='text/css'>

<!-- IE Fix for HTML5 Tags -->
<!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>
		window.jQuery
				|| document
						.write('<script src="js/jquery-1.7.1.min.js"><\/script>')
	</script>

<script type="text/javascript">
$(document).ready(function(){
$('.warningjs').remove();
$.get("token.php",function(txt){
  $(".secure").append('<input type="hidden" name="ts" value="'+txt+'" />');
});
});
</script>
</head>
<body>
	<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
	<div id="content">
		<h1>Contact me</h1>

		<p class="warningjs">JavaScript must be enabled on your browser to use
			this contact form.</p>

		<?php
		/**
		 * This function allows to validate that there isn't any mail injection in a submitted string
		 * @param unknown_type $str this String will contain the mail adress provided by the user
		 * @return boolean true if the String submited by the user contains an injection
		 */
		function isInjected($str)
		{
			$injections = array('(\n+)',
					'(\r+)',
					'(\t+)',
					'(%0A+)',
					'(%0D+)',
					'(%08+)',
					'(%09+)'
			);
			$inject = join('|', $injections);
			$inject = "/$inject/i";
			if(preg_match($inject,$str))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		$proceed = false;
		// We let 15 minutes to the uset to complete and submit the contact form
		$seconds = 60*15;

		//First, check if the form's data has been provided
		if(isset($_POST['username']) && isset($_POST['usermail']) && isset($_POST['message']) && isset($_POST['subject'])) {
			//Then get the token related stuff, used for some security
			if(isset($_POST['ts']) && isset($_COOKIE['token']) && $_COOKIE['token'] == md5('secret-salt'.$_POST['ts'])) $proceed = true;
			//Check if the time allowed to the user is respected
			if($proceed && !(((int)$_POST['ts'] + $seconds) < mktime())) {
				//Remove special chars from every user provided data
				$username = htmlspecialchars($_POST['username']);
				$usermail = htmlspecialchars($_POST['usermail']);
				$message = htmlspecialchars($_POST['message']);
				$subject = htmlspecialchars($_POST['subject']);
				//Create headers for the mail to send
				$headers ='From: '.$username.'<'.$usermail.'>'."\n";
				$headers .='Reply-To: '.$usermail."\n";
				$headers .='Content-Type: text/plain; charset="iso-8859-1"'."\n";
				$headers .='Content-Transfer-Encoding: 8bit';
				//Add the site provided by the user
				if(isset($_POST['usersite'])) {
					$message = $message."\n website: ".htmlspecialchars($_POST['usersite']);
				}
				//If everything's ok
				if($username && $usermail && $message && $subject) {
					//We validate that the mail provided by our user is actually a valid email, and we ensure that it doesn't contain any injection
					if(filter_var($usermail, FILTER_VALIDATE_EMAIL) && !(isInjected($usermail))) {
						//Then, we actually send the mail
						if(mail('YOUR-EMAIL-HERE', $subject, $message, $headers)) {
							?>
		<h2>Thanks !</h2>
		<p>Your message has been sent successfuly !</p>
		<?php
						} else {
							?>
		<h2>Oops !</h2>
		<p>Something went wrong while sending your message...</p>
		<?php
						}
					} else {
						?>
		<h2>Oops !</h2>
		<p>The mail you provided is not a valid one... Please check it and
			submit again ;)</p>
		<?php	
					}
				} else {
					?>
		<h2>Oops !</h2>
		<p>You need to provide each mandatory information to be able to send
			an email...</p>
		<?php
				}
			} else {
				?>
		<h2>Oops !</h2>
		<p>Security protection... Please try again, and submit your form
			quicker ;)</p>
		<?php 
			}
		} else {
			?>
		<form action="./index.php" method="post" autocomplete="on" class="secure">
			<p>
				<label for="username" class="iconic user"> Name <span
					class="required">*</span>
				</label> <input type="text" name="username" id="username"
					required="required" placeholder="Hi friend, how may I call you ?" />
			</p>

			<p>
				<label for="usermail" class="iconic mail-alt"> E-mail address <span
					class="required">*</span>
				</label> <input type="email" name="usermail" id="usermail"
					placeholder="I promise I hate spam as much as you do"
					required="required" />
			</p>

			<p>
				<label for="usersite" class="iconic link"> Website </label> <input
					type="url" name="usersite" id="usersite"
					placeholder="eg: http://www.miste.com" />
			</p>

			<p>
				<label for="subject" class="iconic quote-alt"> Subject <span
					class="required">*</span>
				</label> <input type="text" name="subject" id="subject"
					placeholder="What would you like to talk about?"
					required="required" />
			</p>

			<p>
				<label for="message" class="iconic comment"> Message <span
					class="required">*</span>
				</label>
				<textarea name="message" id="message"
					placeholder="Don't be shy, live me a friendly message and I'll answer as soon as possible "
					required="required"></textarea>
			</p>
			<p class="indication">
				All fields with a <span class="required">*</span> are required
			</p>

			<input type="submit" value="★  Send the mail !" />

		</form>
		<?php }?>
	</div>
</body>
</html>
