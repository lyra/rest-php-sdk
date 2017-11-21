# Changelog

### V3.1.0 (WORK IN PROGRESS)

- version is now defined in the web-service name (use V3/Charge/SDKTest instead of Charge/SDKTest)
- add $client->checkHash($hashKey) method to check POST data answer signature
- add $client->getLastCalculatedHash() get the last calculated hash by checkHash()
- add $client->getClientEndPoint() to allow to test a javascript client on a different server
- add $client->getParsedAnswer() helper to get POST data easily 
- composer ext-curl deps moved to suggest, refs #2

### V3.0.6 2017-05-15

- add setUsername and setPassword methods

### V3.0.4 2017-01-31

- Adding endpoint support
- Add Dockerfile with unzip and composer for local tests

### v3.0.3 2017-01-30

- Add file_get_contents fallback when CURL is not installed
- Add CA root certificate to fix WAMP curl + https 
- Rename namespace to LyraNetwork

### v3.0.1 2016-12-05

- Adding autoload file if you don't like composer
- More stuff in the readme file

### v3.0.0 2016-12-01

- Initial version