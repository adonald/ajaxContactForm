<?php
/*
 * AJAX contact form for website
 * Version: 0.1.0
 * Author: Alex Donald
 * Website: https://www.adonald.co.uk
 * Date: 2016-01-08
 * License: MIT
 */
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/ajaxContactForm.class.php');

/*==============================================================================
 * Set up options for form submission here
 *============================================================================*/
$form_options = [
    // Who will the form submission be sent to?
    'send_to_name'       => 'John Smith',
    'send_to_email'      => 'john.smith@example.com',
    // Who will the form submission appear to come from?
    'send_from_name'     => 'AJAX Contact Form',
    'send_from_email'    => 'website@server.domain.com',
    // Do not include user input here - it will be used in the email headers
    'send_email_subject' => 'Hello from AJAX contact form',
    // True for HTML email, false for plain text
    'send_html_email'    => true,
    // Message to display to user upon form submission - just text please!
    'success_message'    => 'Good news! Your message has been sent, and we will get back to you asap.',
    'error_message'      => 'There has been a problem, please try again later.',
    ];

// Initialise ajaxContactForm object with above options
$ajaxContactForm = new ajaxContactForm($form_options);

/*==============================================================================
 * Gather submitted POST values and decide on how to respond
 *============================================================================*/
$form_input = [
    'form_name'    => $_POST['name'],
    'form_email'   => $_POST['email'],
    'form_message' => $_POST['message'],
    ];

if (empty($_POST['ajax']))
{
    // Ajax value is not present, the visitor came to this page directly
    // Do not process form, just display HTML page
    $html = <<<HTML
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title>AJAX contact form</title>
</head>
<body>
<h1>Oops, it appears that something went wrong!</h1>
<p>If you were trying to contact me, please return to the
<a href="/">contact page</a> to fill out the contact form</p>
</body>
</html>
HTML;

    echo $html;
}
elseif ($_POST['ajax'] == 'true')
{
    // Ajax value is true, form submitted by ajax (probably)
    // Process form and respond with json object
    
    // Attempt to send email, and retrieve result of action
    $result = $ajaxContactForm->send_email($form_input);
    
    header('Content-type: application/json');
    echo json_encode($result);
    
}
else
{
    // Ajax value is false, form submited without javascript
    // Process form and display result in HTML page
    
    // Attempt to send email, and retrieve result of action
    $result = $ajaxContactForm->send_email($form_input);
    
    $success = ($result['success']) ? 'Success!' : 'Oops!';
    $message = $result['message'];

    $html = <<<HTML
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title>$success | AJAX contact form</title>
</head>
<body>
<h1>$success</h1>
<p>$message</p>
<p>Return to the <a href="/">contact page</a> to try again</p>
</body>
</html>
HTML;

    echo $html;
}
