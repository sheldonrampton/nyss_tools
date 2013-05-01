The SenatorData class provides a means of submitting an API query
to retrieve information useful for site integration, including the node
ID of the senator's home page, short name and district.

The constructor for a SenatorData object takes two parameters: 
api_key and domain_name, which Drupal's Services module requires for 
key authentication. The object's get method returns an  array 
(senator1, senator2, senator3, ...) where each instance of senator1, 
senator2, etc. is an array of the form:
  Array
    (
      'nid' => node ID of the senator's home page on NYSenate.gov
      'senator_name' => The senator's name
      'short_name' => the senator's short name (defines the 
                      domain name a Bluebird instance
      'district' => the senator's district
      'email' => the senator's email address
      'path_value' => defines the URL to the senator's home page.
                      For example, a path_value of 'tony-avella'
                      defines a home page URL of
                      http://www.nysenate.gov/senator/tony-avella
    )
