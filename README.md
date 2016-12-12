# ci-dynamodb-session-driver
A DynamoDB session driver for CodeIgniter 3.x

## Usage

To use, simply specify to use DynamoDB for session storage in your configuration file.

Open your `application/config/config.php` and, set "dynamodb" to "sess_diriver"
on "Session Variables" section.
Also, set the table name that you will use to "sess_save_path".
The table you specified is need to create in advance.

```php
$config['sess_driver'] = 'dynamodb';
$config['sess_save_path'] = 'my_session';
```

### AWS access credentials
If you use EC2 and that instances are already given a IAM instance role,
you don't need to config any credentials for this library. Otherwise, you
need to set AWS access key and secret to your configuration file.

Open `application/config/sess_dynamo.php` and, set appropriate credentials to
"sess_dynamo_key" and "sess_dynamo_secret".

## Installation

Copy the application/libraries/Session/drivers/Session_dynamodb_driver.php
and application/config/sess_dynamo.php files into your "applicaion" directory.

If you install with composer, edit your composer.json and add following packages.
After composer install the packages, you need to copy these files as same as above. 

```json
    "sabmeua/ci-dynamodb-session-driver": "*"
```

## Requirements

* aws/aws-sdk-php 3.*
