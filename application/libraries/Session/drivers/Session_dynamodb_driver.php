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
class CI_Session_dynamodb_driver extends CI_Session_driver implements SessionHandlerInterface {

    /**
     * DynamoDb session handler
     *
     * @var Aws\DynamoDb\SessionHandler
     */
    protected $_handler;
    
    
    /**
     *
     * @var boolean
     */
    private $default_garbage_collector;

    /**
     * Class constructor
     *
     * @param   array   $params Configuration parameters
     * @return  void
     */
    public function __construct(&$params) {
        parent::__construct($params);
        $this->CI = & get_instance();
        $this->CI->load->config('sess_dynamo', FALSE, TRUE);    
        $this->default_garbage_collector =  $this->read_config_value('dynamo_default_garbage_collector', TRUE);   
        $dynamodb = new DynamoDbClient($this->read_dynamodb_options());
        $options = $this->read_session_options();
        $this->_handler = SessionHandler::fromClient($dynamodb, $options);
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
    public function open($save_path, $cookie_name) {
        if ($this->_config['match_ip'] === TRUE) {
            $cookie_name .= $_SERVER['REMOTE_ADDR'] . ':';
        }

        return $this->_handler->open($this->_config['save_path'], $cookie_name);
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
    public function read($session_id) {
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
    public function write($session_id, $session_data) {
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
    public function close() {
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
    public function destroy($session_id) {
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
    public function gc($maxlifetime) {
        if ($this->default_garbage_collector) {
            $this->_handler->garbageCollect();
        }
        return $this->_handler->gc($maxlifetime);
    }

    /**
     * Read dynamodb client config
     * @return []
     */
    private function read_dynamodb_options() {
        $dynamo_opts['region'] = $this->read_config_value('dynamo_region', 'us-east-1');
        log_message('debug', 'Dynamodb Session Region: ' . $dynamo_opts['region']);

        $dynamo_opts['version'] = $this->read_config_value('dynamo_version', 'latest');
        log_message('debug', 'Dynamodb Session Version: ' . $dynamo_opts['version']);

        $dynamo_opts['credentials'] = $this->read_credentials_config();
        log_message('debug', 'Dynamodb Session Credentials');
        return $dynamo_opts;
    }

    /**
     * Read dynamodb session options
     * @return []
     */
    private function read_session_options() {
        $handler_opts['table_name'] = $this->read_config_value('save_path', 'sessions', FALSE);
        log_message('debug', 'Dynamodb Session Table: ' . $handler_opts['table_name']);

        $handler_opts['session_lifetime'] = $this->read_config_value('expiration', 7200, FALSE);
        log_message('debug', 'Dynamodb Session Lifetime: ' . $handler_opts['session_lifetime']);
        
        $handler_opts['hash_key'] = $this->read_config_value('dynamo_hash_key', 'id');
        log_message('debug', 'Dynamodb Session Hash Key: ' . $handler_opts['hash_key']);
        
        $handler_opts['consistent_read'] = $this->read_config_value('dynamo_consistent_read', TRUE);
        $handler_opts['locking'] = $this->read_config_value('dynamo_locking', FALSE);
        $handler_opts['batch_config'] = $this->read_config_value('dynamo_batch_config', []);
        $handler_opts['max_lock_wait_time'] = $this->read_config_value('dynamo_max_lock_wait_time', 10);
        $handler_opts['min_lock_retry_microtime'] = $this->read_config_value('dynamo_min_lock_retry_microtime', 5000);
        $handler_opts['max_lock_retry_microtime'] = $this->read_config_value('dynamo_max_lock_retry_microtime', 50000);
        return $handler_opts;
    }

    /**
     * Read Config value
     * @param string $value Value name without 'sses_'
     * @param type $default Daefault value
     * @param boolean $read_from_config_file If is true read values from config.php first and sses_dynamo.php, 
     * if is false only read values from config.php 
     * @return type
     */
    protected function read_config_value($value, $default = NULL, $read_from_config_file = TRUE) {
        if (isset($this->_config[$value])) {
            return $this->_config[$value];
        } else if ($read_from_config_file) {
            $config_value = config_item('sess_' . $value);
            if (isset($config_value)) {
                return $config_value;
            }
        }
        return $default;
    }

    /**
     * Read dynamodb credentials config
     * @return []
     */
    protected function read_credentials_config() {
        $credential_key = $this->read_config_value('dynamo_key');
        if (isset($credential_key)) {
            $credential_secret = $this->read_config_value('dynamo_secret');
            if (isset($credential_secret)) {
                $credential['key'] = $credential_key;
                $credential['secret'] = $credential_secret;
                return $credential;
            }
        }
        return NULL;
    }

}
