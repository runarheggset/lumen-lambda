var spawn = require('child_process').spawn;
var parser = require('http-string-parser');

exports.handler = function(event, context) {
    // Sets some sane defaults here so that this function doesn't fail when it's not handling a HTTP request from
    // API Gateway.
    var requestMethod = event.httpMethod || 'GET';
    var serverName = event.headers ? event.headers.Host : '';
    var requestUri = event.path || '';
    var headers = {};
    var queryParamsStr = '';

    // Convert all headers passed by API Gateway into the correct format for PHP CGI. This means converting a header
    // such as "X-Test" into "HTTP_X-TEST".
    if (event.headers) {
        Object.keys(event.headers).map(function (key) {
            headers['HTTP_' + key.toUpperCase()] = event.headers[key];
        });
    }

    // create query parameters string
    if (event.queryStringParameters) {
        var queryParams = Object.keys(event.queryStringParameters).map(function(key) {
            var obj = key + "=" + event.queryStringParameters[key];
            return obj;
        });
        queryParamsStr = queryParams.join("&");
    }

    // Spawn the PHP CGI process with a bunch of environment variables that describe the request.
    var php = spawn('./php-cgi', ['lumen/public/index.php'], {
        env: Object.assign({
            REDIRECT_STATUS: 200,
            REQUEST_METHOD: requestMethod,
            SCRIPT_FILENAME: 'lumen/public/index.php',
            SCRIPT_NAME: '/index.php',
            PATH_INFO: '/',
            SERVER_NAME: serverName,
            SERVER_PROTOCOL: 'HTTP/1.1',
            REQUEST_URI: requestUri,
            QUERY_STRING: queryParamsStr
        }, headers, process.env)
    });

    // Listen for output on stdout, this is the HTTP response.
    var response = '';
    php.stdout.on('data', function(data) {
        response += data.toString('utf-8');
    });

    // When the process exists, we should have a complete HTTP response to send back to API Gateway.
    php.on('close', function(code) {
        // Parses a raw HTTP response into an object that we can manipulate into the required format.
        var parsedResponse = parser.parseResponse(response);

        // Signals the end of the Lambda function, and passes the provided object back to API Gateway.
        context.succeed({
            statusCode: parsedResponse.statusCode || 200,
            headers: parsedResponse.headers,
            body: parsedResponse.body
        });
    });
};
