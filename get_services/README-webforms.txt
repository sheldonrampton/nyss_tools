The WebformResponses class provides a means of submitting an API query
to retrieve webform responses via the webform_services module for Drupal.
File xml-rpc-webforms-test.php provides some somple code showing how the
query works.

The constructor for a WebformResponses object takes two parameters: 
api_key and domain_name, which Drupal's Services module requires for 
key authentication. The object's get method then takes an associative
array $params, which can contain any of the following optional key-value 
pairs to specify the responses to be returned:

'nid' => an integer value for the node ID of a specific webform. 
If this parameter is supplied, results are returned only for that nodeform. 
If no nid is supplied, the method returns results for all webforms.

'start_date' => an integer value for the earliest timestamp from which 
results are to be returned.

'end_date' => an integer value for the latest timestamp from which 
results are to be returned.

'start_sid' => Each submission has a unique sequentially "submission ID" or sid. 
The start_sid is an integer value for the smallest (and therefore earliest) 
sid from which results are to be returned.

'end_sid' => an integer value for the largest (and therefore smallest) 
sid from which results are to be returned.

'uid' => Each Drupal user has a unique integer user ID (uid). Anonymous users 
have a uid of 0. If this parameter is supplied, results are returned only 
for the specified uid.

'status' => the published status of the webforms from which to retrieve submissions.
             0 => return results from both published and unpublished webforms
             1 => return results only from published webforms
             2 => return results only from unpublished webforms

'limit' => A integer specifying the maximum number of results to be returned 
(default 100). A limit of zero is interpreted to mean, "return all results." 
Warning: an excessively large limit may produce a memory and and result in 
no results being returned.


A WebformResponse object's get method returns an associative array of the form:
    Array
        (
            'start_sid' => the lowest submission id in the returned set
            'end_sid' => the highest submission id in the return set
            'start_date' => the lowest submission date in the returned set
            'end_date' => the highest submission date in the returned set
            'nids' => Array(
                 [nid1] => [results1]
                 [nid2] => [results2]
                 ...
              )
        )
where each instance of results1, results2, etc. is an array of the form:
    Array
        (
            'webform_title' => the title of the webform
            'senator_short_name' => the short name of a senator, which defines 
                                    his/her related Bluebird instance. For example,
                                    the short name for Eric Adams is "adams"
                                    and the path to his Bluebird instance is
                                    "adams.crm.nysenate.gov."
            'senator_district' => the senator's district
            'status' => the published status of the webform (0=unpublished, 1=published)
            'fields' => an array giving field definitions for the webform
            'sids' => Array(
                 [sid1] => [submission1]
                 [sid2] => [submission2]
                 ...
              )
        )

and where each instance of submission1, submission2, etc. is an array of 
values from a single webform submission, as returned from function 
webform_service_submission_values() in the webform_service module for Drupal. 
The array returned by webform_service_submission_values() has the following
structure:

where each instance of results1, results2, etc. is an array of the form:
    Array
        (
            'nid' => the webform's node ID
            'submitted' => the timestamp for when the response was submitted
            'uid' => the user ID of the person who submitted the response
            'name' => the user name of the person who submitted the response
            'is_draft' => a boolean value for whether the submission is a draft
            'values' => Array(
                 [form_key1] => Array(
                   'data' => a numeric or text value for the submission response. 
                             Example: For a "select options" form component, the value
                             would be an integer specifying which option was
                             selected. 
                   'view' => an ASCII text representation intended to display the
                             submission response. For a "select options" form
                             component, this view would contain the text of the
                             chosen option.
                 ),
                 [form_key2] => Array(
                   'data' => ...
                   'view' => ...
                 ),
                 ...
              )
        )
In the above structure, [form_key1], [form_key2], etc. would be the unique machine
names associated with each component (form field) in the webform.

Finally, the 'fields' array which gives field definitions for a webform has the
following structure:
    Array
        (
          [1] => [definition1]
          [2] => [definition2]
          ...
        )
where each instance of definition1, definition2, etc. is an associative array
detailing the characteristics of one of the components in the webform, as follows:
    Array
        (
          'nid' => the webform's node ID
          'cid' => a unique numerical "component ID"
          'pid' => (not sure what this represents)
          'form_key' => the unique machine name associated with this component
          'name' => the display name of this component
          'type' => the field type, e.g., textfield, select, email address, etc.
          'value' => (not sure what this represents: a default value, perhaps?)
          'extra' => Array
              (
                  'description' => an optional description to help users fill
                                   out the form field
                  'width' => the visual width of the form field
                  'maxlength' => the maximum length of the field (number of characters allowed)
                  'field_prefix' => the text that appears as a label for the form field
                  'disabled' => boolean; is this form field disabled?
                  'field_suffix' => (not sure what this represents)
                  'attributes' => Array(
                        (not sure what this represents)
                      )

                  'unique' => boolean
                  'title_display' => boolean: should the field display its title?
                  'conditional_component' => (not sure what this represents)
                  'conditional_operator' => a comparison operator: "<" or "=" or ">"
                  'conditional_values' => (not sure what this represents)
              )

          'mandatory' => boolean; is this a required field?
          'weight' => an integer which controls the order in which form fields should appear;
                      fields with higher weight appear lower in the form
        )

