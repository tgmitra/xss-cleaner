## Cross-site scripting (XSS) cleaner

Cross-site scripting (XSS) is an injection attack which is carried out on Web applications that accept input, but do not 
properly separate data and executable code before the input is delivered back to a user’s browser. XSS attacks occur when an attacker uses a web application to send malicious code, generally in the form of a browser side script, to a different end user. Flaws that allow these attacks to succeed are quite widespread and occur anywhere a web application uses input from a user within the output it generates without validating or encoding it.

## Requirements

1. PHP 5.4 or greater

## Installation

Drag and drop the **/security.class.php** and **example.php** (optional) files into your application's directories. 
To use add `require_once( __DIR__ . "/security.class.php" );` at the top of your controllers to load it into the scope. 

Additionally, as mentioned in example.php use `$filter_payload = new filter_payload();` and then just call the function 
`$filter_payload->clean_request_payload();` to clean all request payload.

Alternatively you can pass specific request parameter also on the clean_request_payload() for example  
$_GET = `$filter_payload->clean_request_payload($_GET);`