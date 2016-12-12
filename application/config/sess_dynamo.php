<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| AWS region to connect to DynamoDb
|--------------------------------------------------------------------------
| This has to be set a string of available regions name.
*/
$config['sess_dynamo_region'] = 'ap-northeast-1';

/*
|--------------------------------------------------------------------------
| AWS access key id
|--------------------------------------------------------------------------
| If you use IAM instance role, this value may be NULL or this doesn't
| need to be specified.
*/
$config['sess_dynamo_key'] = NULL;

/*
|--------------------------------------------------------------------------
| AWS access secret
|--------------------------------------------------------------------------
| If you use IAM instance role, this value may be NULL or this doesn't
| need to be specified.
*/
$config['sess_dynamo_secret'] = NULL;

/*
|--------------------------------------------------------------------------
| The version of DynamoDb service
|--------------------------------------------------------------------------
| This values is required. "latest" is also OK, but it isn't recommeded.
| Because, specifying a version constraint that your code will not be
| affected by some changes made to API.
*/
$config['sess_dynamo_version'] = '2012-08-10';

/**
 * DynamoDb Session Handler options
 *
 * The following options are as is used for Aws\DynamDb\SessionHandler.
 * The detail of these items can be found:
 *   http://docs.aws.amazon.com/aws-sdk-php/v3/guide/service/dynamodb-session-handler.html.
*/

/*
|--------------------------------------------------------------------------
| Name of hash key in table
|--------------------------------------------------------------------------
| The name of the hash key in the DynamoDB sessions table.
| This defaults to id.
*/
$config['sess_dynamo_hash_key'] = 'id';

/*
|--------------------------------------------------------------------------
| Whether or not to use consistent reads
|--------------------------------------------------------------------------
| Whether or not the session handler should use consistent reads for the
| GetItem operation. This defaults to true.
*/
$config['sess_dynamo_consistent_read'] = true;

/*
|--------------------------------------------------------------------------
| Batch options used for garbage collection
|--------------------------------------------------------------------------
| Configuration used to batch deletes during garbage collection. These
| options are passed directly into DynamoDB WriteRequestBatch objects.
| You must manually trigger garbage collection via
| SessionHandler::garbageCollect().
*/
$config['sess_dynamo_batch_config'] = [];

/*
|--------------------------------------------------------------------------
| Whether or not to use session locking
|--------------------------------------------------------------------------
| Whether or not to use session locking. This defaults to false.
*/
$config['sess_dynamo_locking'] = false;

/*
|--------------------------------------------------------------------------
| Max time (s) to wait for lock acquisition
|--------------------------------------------------------------------------
| Maximum time (in seconds) that the session handler should wait to acquire
| a lock before giving up. This defaults to 10 and is only used with
| session locking.
*/
$config['sess_dynamo_max_lock_wait_time'] = 10;

/*
|--------------------------------------------------------------------------
| Min time (μs) to wait between lock attempts
|--------------------------------------------------------------------------
| Minimum time (in microseconds) that the session handler should wait
| between attempts to acquire a lock. This defaults to 10000 and is only
| used with session locking.
*/
$config['sess_dynamo_min_lock_retry_microtime'] = 5000;

/*
|--------------------------------------------------------------------------
| Max time (μs) to wait between lock attempts
|--------------------------------------------------------------------------
| Maximum time (in microseconds) that the session handler should wait
| between attempts to acquire a lock. This defaults to 50000 and is only
| used with session locking.
*/
$config['sess_dynamo_max_lock_retry_microtime'] = 50000;

