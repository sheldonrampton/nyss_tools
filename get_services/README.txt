DESCRIPTION
-----------

This project provides a class library for accessing the email signups via the
New York State Senate's XML-RPC-based Services API.

HOW TO USE THE CLASS LIBRARY
----------------------------

The class library is located in file xmlrpc-api-signups.inc. It creates a SignugGet class:
The file named xml-rpc-signups-test.php contains an example showing how to use it.

The xmlrpc-api-signups library relies upon the XML-RPC for PHP library (current version, 3.0.0beta):
* http://phpxmlrpc.sourceforge.net/
* http://sourceforge.net/projects/phpxmlrpc/files/phpxmlrpc/3.0.0beta/xmlrpc-3.0.0.beta.zip/download

MORE INFORMATION
----------------

The Services Module for Drupal and related documentation can be found at:
http://drupal.org/project/services

The developer documentation for NYSenate.gov explains how to use the node.get and views.get
methods provided by the Services API:
http://www.nysenate.gov/developers/apis

ACKNOWLEDGMENTS
---------------
Thanks to Annabel Bush for writing the proof-of-concept code upon which this
library based.
