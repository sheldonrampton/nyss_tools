The ContactMessages class provides a means of submitting an API query
to retrieve archived email messages that were sent via the contact forms
on NYSenate.gov to individual senators as well as to the entire senate.
File xml-rpc-messages-test.php provides some somple code showing how the
query works.

The constructor for a ContactMessages object takes two parameters: 
api_key and domain_name, which Drupal's Services module requires for 
key authentication. The object's get method then takes an associative
array $params, which can contain any of the following optional key-value 
pairs to specify the responses to be returned:

'start_date' =>
   Number. An optional start date in timestamp format indicating the
   earliest date from which messages are requested.
'end_date' =>
   Number. An optional end date in timestamp format indicating the 
   latest date for which messages are requested.
'start_mid' =>
   Number. An optional start message ID number for the range from 
   which results are desired.
'end_mid' =>
   Number. An optional last message ID number for the range from 
   which results are desired.
'source_form' =>
   String. Options are "senator_mail" for messages to individual senators 
   and "page_mail" for messages to the senate.
'senator_short_name' =>
   String. The short name for the senator receiving the message, 
   which defines the domain name for the senator's Bluebird instance.
'district_number' =>
   Number. The district of the recipient senator.
'limit' =>
   Number. The maximum number of responses to return (default 100). 
   If a limit of "0" is specified, all responses will be returned. 
   Warning: setting no limit could result in timeout or memory errors!

A ContactMessages object's get method returns an associative array of the form:
    Array
        (
            'start_mid' => the lowest submission id in the returned set
            'end_mid' => the highest submission id in the return set
            'start_date' => the lowest submission date in the returned set
            'end_date' => the highest submission date in the returned set
            'item_count' => the number of messages returned
            'items' => Array(
                 [0] => [message1]
                 [1] => [message2]
                 ...
              )
        )
where each instance of message1, message1, etc. is an array of the form:
    Array
        (
            'mid' => a unique message ID number
            'source_form' => The source_form for this message 
                             Options are "senator_mail" for messages to 
                             individual senators and "page_mail" for 
                             messages to the senate.'
            'uid' => the message sender's user ID number (0 if the sender was not logged in)
            'voter_registered' => Whether the message sender is a registered voter
            'first_name' => sender's first name
            'last_name' => sender's last name
            'from_url' => URL to the sender's user profile page on NYSenate.gov (if the sender was
                          logged in)
            'from_email' => sender's email address
            'address' => sender's street address
            'apartment' => sender's apartment number
            'city' => sender's city
            'state' => sender's state
            'zip' => sender's zip code
            'phone' => sender's phone mumber
            'to_name' => recipient's name (if the recipient is an individual senator)
            'to_nid' => node ID of the recipient's Senator node on NYSenate.gov
            'to_district_number' => recipient senator's district
            'to_short_name' => recipient senator's short name (defines the domain name
                               of their Bluebird instance)
            'subject' => the subject line of the message
            'message' => the message body
            'issues' => an array of related issues chosen by the message sender
            'to_email' => the email address to which the message was sent
            'submitted' => timestamp when the message was sent
        )
