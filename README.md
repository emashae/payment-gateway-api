# Payment Gateway API

A simple API for handling transactions, including card number validation and masking.

## Creating the .env.testing File

Create a .env.testing file in the root directory of the project with the following contents:

APP_NAME=Laravel
APP_ENV=testing
APP_KEY=base64:YOUR_APP_KEY
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=test_database
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

QUEUE_CONNECTION=sync
CACHE_DRIVER=array
SESSION_DRIVER=array
