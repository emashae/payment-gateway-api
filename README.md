# Payment Gateway API

A simple API for handling transactions, including card number validation and masking. Below are the instructions for setting up the database, Docker environment, and the .env.testing file. Please ensure to execute the necessary Laravel commands as required.

## Database Setup

Create a schema named 'payment_gateway' in your database.
Update your .env or .env.testing file with the appropriate database connection details (as shown below in the .env.testing section).

## Docker Setup

The Dockerfile and docker-compose.yml files are available in the root directory.
Use these files to set up a containerized environment for your application.

## Creating the .env.testing File

For testing purposes, a separate .env.testing file ensures isolation from the development environment. Create this file in the root directory of your project with the following contents:

```bash
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
```
