<?php 

// mail, pwd and sender name will be constant since we are the sender
const MARKET_APP_MAIL = "market-mail-app@gmail.com";
const PASSWORD = "password-of-mail";
const FULLNAME = "Market App Team";

// this will change since it will be sent to different users
$to = "our-client";
$subject = "Hello from Market App Teams";
$htmlMessage = "<h2>Welcome to our Market app,<h2>
            <p>Your authentication code is <b>bla bla</b></p>";
$nonhtmlMessage = "Welcome to our Market app,\n
                Your authentication code is bla bla";