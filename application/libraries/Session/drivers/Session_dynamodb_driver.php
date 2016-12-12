<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\SessionHandler;

/**
 * CodeIgniter Session DynamoDb Driver
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Sessions
 * @link        https://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_dynamodb_driver extends CI_Session_driver
    implements SessionHandlerInterface
{

    /**
     * DynamoDb session handler
     *
     * @var Aws\DynamoDb\SessionHandler
     */
    protected $_handler;

    // ------------------------------------------------------------------------

    private function _configure()
    {
        $dynamo_opts = [];
        $handler_opts = [];

        if (!isset($this->_config['sess_dynamo_key']))
        {
            log_message('error',
                'Session: "sess_save_path" is empty;' .
                ' No DynamoDb table configured.');
        }
        $dynamo_opts['region'] = config_item('sess_dynamo_region');

        if (!isset($this->_config['sess_dynamo_key']))
        {
            log_message('error',
                'Session: "sess_save_path" is empty;' .
                ' No DynamoDb table configured.');
        }
        $dynamo_opts['version'] = config_item('sess_dynamo_version');

        if (@isset($this->_config['sess_dynamo_key']) &&
            !is_null($this->_config['sess_dynamo_key']))
        {
            $dynamo_opts['credentials']['key'] = $key;
        }

        if (@isset($this->_config['sess_dynamo_secret']) &&
            !is_null($this->_config['sess_dynamo_secret']))
        {
            $dynamo_opts['credentials']['secret'] = $secret;
        }

        if (empty($this->_config['save_path']))
        {
            log_message('error',
                'Session: "sess_save_path" is empty;' .
                ' No DynamoDb table configured.');
        }
        $handler_opts['table_name'] = $this->_config['save_path'];

        if (@isset($this->_config['sess_expiration']))
        {
            $handler_opts['session_lifetime'] = config_item('sess_expiration');
        }

        if (@isset($this->_config['sess_dynamo_max_lock_wait_time']))
        {
            $handler_opts['max_lock_wait_time'] =
                config_item('sess_dynamo_max_lock_wait_time');
        }

        if (@isset($this->_config['sess_dynamo_min_lock_retry_microtime']))
        {
            $handler_opts['min_lock_retry_microtime'] =
                config_item('sess_dynamo_min_lock_retry_microtime');
        }

        if (@isset($this->_config['sess_dynamo_max_lock_retry_microtime']))
        {
            $handler_opts['max_lock_retry_microtime'] =
                config_item('sess_dynamo_max_lock_retry_microtime');
        }

        $this -> _config['dynamodb_options'] = $dynamo_opts;
        $this -> _config['sess_handler_options'] = $handler_opts;
    }

    /**
     * Class constructor
     *
     * @param   array   $params Configuration parameters
     * @return  void
     */
    public function __construct(&$params)
    {
        parent::__construct($params);

        $this->CI =& get_instance();
        $this->CI->load->config('sess_dynamo');
        $this->_configure();

        $dynamodb = new DynamoDbClient(
            $this -> _config['dynamodb_options']);

        $this->_handler = SessionHandler::fromClient(
            $dynamodb, $this->_config['sess_handler_options']);
    }

    // ------------------------------------------------------------------------

    /**
     * Open
     *
     * Sanitizes save_path and initializes connections.
     *
     * @param   string  $save_path  Server path(s), unused
     * @param   string  $name       Session cookie name
     * @return  bool
     */
    public function open($save_path, $cookie_name)
    {
        if ($this->_config['match_ip'] === TRUE)
        {
            $cookie_name .= $_SERVER['REMOTE_ADDR'] .':';
        }

        return $this->_handler->open(
            $this->_config['save_path'], $cookie_name);
    }

    // ------------------------------------------------------------------------

    /**
     * Read
     *
     * Reads session data and acquires a lock
     *
     * @param   string  $session_id Session ID
     * @return  string  Serialized session data
     */
    public function read($session_id)
    {
        return $this->_handler->read($session_id);
    }

    // ------------------------------------------------------------------------

    /**
     * Write
     *
     * Writes (create / update) session data
     *
     * @param   string  $session_id Session ID
     * @param   string  $session_data   Serialized session data
     * @return  bool
     */
    public function write($session_id, $session_data)
    {
        return $this->_handler->write($session_id, $session_data);
    }

    // ------------------------------------------------------------------------

    /**
     * Close
     *
     * Releases locks and closes connection.
     *
     * @return  bool
     */
    public function close()
    {
        return $this->_handler->close();
    }

    // ------------------------------------------------------------------------

    /**
     * Destroy
     *
     * Destroys the current session.
     *
     * @param   string  $session_id Session ID
     * @return  bool
     */
    public function destroy($session_id)
    {
        return $this->_handler->destroy($session_id);
    }

    // ------------------------------------------------------------------------

    /**
     * Garbage Collector
     *
     * Deletes expired sessions
     *
     * @param   int     $maxlifetime    Maximum lifetime of sessions
     * @return  bool
     */
    public function gc($maxlifetime)
    {
        return $this->_handler->gc();
    }
}

