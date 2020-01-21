<?php
/*
 * AJAX contact form for website
 * Version: 0.1.0
 * Author: Alex Donald
 * Website: https://www.adonald.co.uk
 * Date: 2016-01-08
 * License: MIT
 */
 
 class ajaxContactForm {

    private $options = array(
        'send_to_name'       => 'John Smith',
        'send_to_email'      => 'john.smith@example.com',
        'send_from_name'     => 'AJAX Contact Form',
        'send_from_email'    => 'website@server.domain.com',
        'send_email_subject' => 'Hello from AJAX contact form',
        'send_html_email'    => true,
        'success_message'    => 'Good news! Your message has been sent.',
        'error_message'      => 'There has been a problem, please try again later.',
        );
    private $result = array();
    private $boundary = "";
    
    /*==========================================================================
     * Constructor - sets up options from array passed when initiating
     *========================================================================*/
    
    public function __construct($sent_options)
    {
        $this->options = array_merge($this->options, $sent_options);
        $this->boundary = uniqid('np');
    }
    
    /*==========================================================================
     * This function creates the email message body from the submitted
     * information. You can change things here, but be careful!
     *========================================================================*/
    
    private function compile_email_body_html($form_input)
    {
        // Set up array in which to build message body
        $email_body = array();
        
        // Change carriage returns to <br /> tags for HTML version
        $form_message_breaks = nl2br($form_input['form_message']);
        
        // Set boundary for multipart emails (i.e. plain text and HTML combined)
        $email_body[] = "--" . $this->boundary;
        $email_body[] = "Content-Type: text/html; charset=ISO-8859-1\r\n";

        /*
        * Change these lines for HTML emails.
        */
        $email_body[] = "<html><body>";
        $email_body[] = "<h2>You have received a new message from your website contact form.</h2>";
        $email_body[] = "<p>Here are the details:</p>";
        $email_body[] = "<h3>Message from: " . $form_input['form_name'] . "</h3>";
        $email_body[] = "<p>Email address: " . $form_input['form_email'] . "</p>";
        $email_body[] = "<p>" . $form_message_breaks . "</p>";
        $email_body[] = "</html></body>";
        
        // implode array with line breaks to split each entry
        $compiled_body = implode("\r\n", $email_body);
        return $compiled_body;
    }

    private function compile_email_body_text($form_input)
    {
        // Set up array in which to build message body
        $email_body = array();
        
        // Set boundary for multipart emails (i.e. plain text and HTML combined)
        if($this->options['send_html_email']){
            $email_body[] = "--" . $this->boundary;
            $email_body[] = "Content-type: text/plain; charset=ISO-8859-1\r\n";
        }

        /*
        * Change these lines for plain text emails.
        * 
        * plain text version... leaves quotes as html entities e.g.
        * " will display as &#34;
        * ' will display as &#39;
        */
        $email_body[] = "You have received a new message from your website contact form.";
        $email_body[] = "Here are the details:";
        $email_body[] = "Name: " . $form_input['form_name'];
        $email_body[] = "Email: " . $form_input['form_email'];
        $email_body[] = "Message:";
        $email_body[] = $form_input['form_message'];
        
        // implode array with line breaks to split each entry
        $compiled_body = implode("\r\n", $email_body);
        return $compiled_body;
    }
    
    /*==========================================================================
     * 
     * Don't change anything below here unless you understand it!
     * 
     *========================================================================*/
    
    /*==========================================================================
     * Send email
     *========================================================================*/
    
    public function send_email($form_input)
    {
        // Check submitted form values
        $this->check_input_validity($form_input);
        
        if ($this->result['success'])
        {
            $to         = $this->remove_line_breaks($this->options['send_to_name']
                            . " <" . $this->options['send_to_email'] . ">");
            
            $subject    = $this->remove_line_breaks($this->options['send_email_subject']);
            
            // compile form message into email body
            if($this->options['send_html_email']){
                $email_body = $this->compile_email_body_html($form_input)
                            . "\r\n\r\n"
                            . $this->compile_email_body_text($form_input);
            } else {
                $email_body = $this->compile_email_body_text($form_input);
            }
            
            // compile headers
            $headers    = $this->compile_headers($form_input);
            
            // send email
            $mail_sent  = mail($to, $subject, $email_body, $headers);
            $mail_sent_message = $mail_sent ? $this->options['success_message']
                                            : $this->options['error_message'];
            
            $this->set_result($mail_sent, $mail_sent_message);
        }
        
        // Return result
        return $this->result;
    }
    
    /*==========================================================================
     * Getters and setters
     *========================================================================*/
    
    private function set_result($success, $message)
    {
        $this->result['success'] = $success;
        $this->result['message'] = $message;
    }
    
    /*==========================================================================
     * Private helper methods
     *========================================================================*/
    
    private function check_input_validity($form_input)
    {
        // Set up defaults
        $check_input_validity = true;
        $check_input_warning  = '';

        // Check for empty fields
        if(empty($form_input['form_name']))
        {
            $check_input_validity = false;
            $check_input_warning  = 'Please fill out your name.';
        }
        elseif(empty($form_input['form_email']))
        {
            $check_input_validity = false;
            $check_input_warning  = 'Please fill out your email.';
        }
        elseif(!filter_var($form_input['form_email'], FILTER_VALIDATE_EMAIL))
        {
            $check_input_validity = false;
            $check_input_warning  = 'Please use a valid email address.';
        }
        elseif(empty($form_input['form_message']))
        {
            $check_input_validity = false;
            $check_input_warning  = 'Please include a message.';
        }
        elseif (preg_match('/\\[rn]|\n|\r|%0[AD]/i', $form_input['form_email']) ||
                // I think the above line fails "from" email addresses
                // containing literal, typed, or encoded newline characters
                preg_match('/\\[rn]|\n|\r|%0[AD]/i', $form_input['form_name']))
                // I think the above line fails "name" entries containing
                // literal, typed, or encoded newline characters
        {
            $check_input_validity = false;
            $check_input_warning  = 'Sorry, your name or email address appear to be incorrect, please try again.';
        }
        
        $this->set_result($check_input_validity, $check_input_warning);
    }
    
    private function remove_line_breaks($input_string)
    {
        $output_string = preg_replace( "/\r|\n/", " ", $input_string );
        return $output_string;
    }
    
    private function compile_headers($form_input)
    {
        // Set up headers - be careful; don't include user input here unless you
        // know what you are doing. Check
        // http://www.damonkohler.com/2008/12/email-injection.html
        // for information
        
        $headers      = array();
        $headers[]    = "MIME-Version: 1.0";

        if($this->options['send_html_email']){
            $headers[] = "Content-Type: multipart/alternative;boundary=" . $this->boundary;
        } else {
            $headers[] = "Content-type: text/plain; charset=ISO-8859-1";
        }
        
        $headers[]    = "From: " . $this->remove_line_breaks($this->options['send_from_name']
                            . " <" . $this->options['send_from_email'] . ">");
        
        $headers[]    = "Reply-To: " . $this->remove_line_breaks($form_input['form_name']
                            . " <" . $form_input['form_email'] . ">");
        
        $headers[]    = "X-Mailer: PHP/".phpversion();
        
        // implode array with line breaks to split each entry
        $compiled_headers = implode("\r\n", $headers);
        
        return $compiled_headers;
    }

 }