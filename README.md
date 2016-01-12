# ajaxContactForm

Simple AJAX & PHP contact form with fallback for when JavaScript is disabled.

Created by [Alex Donald](https://www.adonald.co.uk).

## Quick Start

1. Edit the main options in `ajaxContactForm.php`.
2. Upload the `contact` directory to your website.
3. Create a simple contact form on your site that calls `ajaxContactForm.php`
   using `POST`.
4. Include an empty div for the result message `<div id="result"></div>`.
5. Include the `ajaxContactForm.js` file on the same page as the form.
6. Tell **ajaxContactForm** what your form and result element IDs are (see below).
7. Sit back and watch the emails come in!

## PHP Options

Here are all the options you can change in `ajaxContactForm.php`:

```php
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
```

It is not necessary to include all of these options, the above list shows the
defaults that will be used if the options are omitted from this file.

Obviously you will want to change the `send_to_name` and `send_to_email` to your
own; otherwise you won't get any emails!

Setting the `send_from_name` and `send_from_email` is advisable to reduce the
chance of any emails you receive getting caught by your spam filter. Usually
you can choose any name, but the domain name of the email should reflect the
FQDN of your server (on shared hosting, this is unlikely to be your domain name).

Changing the `send_email_subject` will make it easier for you to filter search
your inbox for emails from your website.

You can choose to receive HTML or plain text emails from the form using the
`send_html_email` option. Plain text emails will encode both single and double
quotes as HTML entities. i.e. `'` will become `&#39`, and `"` will become `&#34`.

The `success_message`, and `error_message` options will change the text seen in
the result div.

## HTML Form

Currently the contact form accepts three inputs: `name`, `email`, and `message`.
I do have plans to build upon this so any number of arbitrary inputs can be used,
but these three are all for now.

The key HTML elements that the scripts rely on are:

1. A form with the ID `id="ajaxContactForm"` - If you change this, make sure to
   change the option in the JavaScript (see below).
2. The hidden input `ajax` - this is dynamically changed to true as a crude
   check for JavaScript.
3. The three inputs named `name`, `email`, and `message`.
4. A submit button named `submit`.
5. An empty div eith the ID `id="ajaxContactFormResult"` - If you change this,
   make sure to change the option in the JavaScript (see below).

```html
<form action="/contact/ajaxContactForm.php" method="post" name="ajaxContactForm" id="ajaxContactForm" novalidate>
    <input type="hidden" id="ajax" name="ajax" value="false">
    
    <label for="name">Name: </label>
    <input type="text" placeholder="Your name *" id="name" name="name" required>
    
    <label for="email">Email: </label>
    <input type="email" placeholder="Your email address *" id="email" name="email" required data-error="Please enter a valid email address.">
    
    <label for="message">Message: </label>
    <textarea rows="6" placeholder="Your message *" id="message" name="message" required></textarea>
    
    <button type="submit" name="submit">Send Message</button>
</form>
<div id="ajaxContactFormResult"></div>
```

## JavaScript

You will need to include `ajaxContactForm.js`, and tell it what the element IDs
are for both your form, and the result message div:

```html
    <script type="text/javascript" src="/contact/ajaxContactForm.js"></script>
    <script type="text/javascript">
        (function(acf){
            // Set the element IDs for the contact form, and the results div.
            // acf.setID(contactFormID, resultDivID)
            acf.setId("ajaxContactForm", "ajaxContactFormResult");
            acf.initialise();
        })(ajaxContactForm);
    </script>
```

## Fallback Behaviour

If the user has JavaScript disabled in their browser, the contact form will
fallback to standard POST submission. It also deals gracefully with users
requesting the page directly.

There is a hidden input `name="ajax"` that supplies `true` or `false` upon form
submission, as a (very) simple check for JavaScript. This value defaults to
`false`, but is changed to `true` by JavaScript after the page loads.

At the bottom of `ajaxContactForm.php`, this value is used to decide what to
send back. If it is `true`, a JSON response is returned that can be used to
display succes/error messages to the user directly on the same page as the form.
If it is `false` or undefined, some simple HTML pages are generated.

These HTML pages can be edited to your preference. By default, they contain a
redirect to the root of your site if no form submission is found. If the form
has been submitted without JavaScript, or brute forced using cURL or similar,
then **ajaxContactForm** will attempt to send the email, displaying the
success/error message on a simple HTML page aftwerwards.

These HTML pages can be edited to suit.

## Work in Progress

1. Implement session ID
2. Expand form to accept various structures of input
