<?php
namespace Api;

/**
 * Abstract base class that defines common core functinality among action handlers for requests to website APIs. The
 * class expects to handle POST requests with request bodies containing JSON encoded data.
 */
class ActionHandler {

    /** @var \Util\Logger */
    protected $logger;

    /** @var mixed[] */
    protected $queryString;

    /** @var mixed[] */
    protected $requestBody;

    /**
     * Constructs a new instance of the action handler.
     * 
     * The handler will decode the JSON body and the query string associated with the request and store the results
     * internally.
     *
     * @param [type] $logger
     */
    public function __construct($logger) {
        $this->logger = $logger;
        $this->requestBody = \json_decode(\file_get_contents('php://input'), true);
        $this->queryString = array();
        \parse_str($_SERVER['QUERY_STRING'], $this->queryString);
    }

    /**
     * Verifies that the provided parameter name exists in the requst body. If it does not, the server will send
     * a BAD_REQUEST response to the client and the script will exit.
     *
     * @param string $name the name of the paramter expected in the request body
     * @param string|null $message the message to send back to the client if the check fails. I null, a default message
     * will be sent
     * @return void
     */
    public function requireParam($name, $message = null) {
        if (!\array_key_exists($name, $this->requestBody)) {
            $message = $message == null ? "Missing required request body parameter: $name" : $message;
            $this->respond(new Response(Response::BAD_REQUEST, $message));
        }
    }

    /**
     * Fetches the provided parameter from the request body. This will not work for nested parameters.
     * 
     * If the `$require` parameter is set to `true` and the requested key is not in the request body, the script
     * will respond with a BAD_REQUEST and terminate.
     *
     * @param string $param the name of the request body parameter to fetch
     * @param boolean $require indicates whether to require the parameter. Defaults to true.
     * @param string $message a message to output if the required parameter is not present. Only used when $require is
     * true.
     * @return mixed|null the value if it exists, null otherwise
     */
    public function getFromBody($param, $require = true, $message = null) {
        if($require) {
            $this->requireParam($param);
            return $this->requestBody[$param];
        } else {
            // Still check so that we don't get an error
            return isset($this->requestBody[$param]) ? $this->requestBody[$param] : null;
        }
        
    }

    /**
     * Sends the provided response object to the client.
     * 
     * This function will exit the script after invocation.
     *
     * @param Response $response the response to send back to the client
     * @return void
     */
    public function respond($response) {
        $this->logger->info('Sending HTTP response: ' . $response->getCode() . ': ' . $response->getMessage());
        \header('Content-Type: application/json; charset=UTF-8');
        $code = $response->getCode();
        header("X-PHP-Response-Code: $code", true, $code);
        echo $response->serialize();
        exit(0);
    }

    /**
     * Get the value of requestBody
     */ 
    public function getRequestBodyAsArray() {
        return $this->requestBody;
    }

    /**
     * Get the value of queryString
     */ 
    public function getQueryStringAsArray() {
        return $this->queryString;
    }
}
