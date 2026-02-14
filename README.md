# LARAVEL EMAIL SERVICES

## Configure .env

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.domain.com
MAIL_PORT=587
MAIL_USERNAME=email@mail.com
MAIL_PASSWORD=password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=email@mail.com
MAIL_FROM_NAME="Email Services"

MAIL_API_KEY=12345678
```

## Setup project

- clone this project
- composer update
- php artisan key:generate
- php artisan serve
- then new terminal for queue : php artisan queue:work

## Queue

- For development

```bash
php artisan queue:work
```

- For production (recommended):

```bash
php artisan queue:work --tries=3 --timeout=60
```

## Use this api

```bash
POST http://localhost:8000/api/email-services?apikey=123456789
```

- For api queue, add url /q/

```bash
POST http://localhost:8000/api/q/email-services?apikey=123456789
```

```json
{
    "to": "email@domain.com",

    "subject": "HELLO WORLD FROM EMAIL",

    "body": "BODY EMAIL"
}
```

### Available parameter :

to : single (email@domain.com) or multi (email@domain.com;email2@gmail.com)

subject : Subject Email

body : Body Email

cc : single (email@domain.com) or multi (email@domain.com;email2@gmail.com)

bcc : single (email@domain.com) or multi (email@domain.com;email2@gmail.com)

reply : single (email@domain.com) or multi (email@domain.com;email2@gmail.com)

attachments (json)

```json
{
    "to": "email@domain.com",

    "subject": "TESTING JSON",

    "body": "BODY JSON",

    "attachments": [
        {
            "filename": "gambar.jpg",

            "url": "https://png.pngtree.com/element_our/20190528/ourmid/pngtree-file-icon-image_1128287.jpg",

            "mime": "image/jpeg"
        },

        {
            "filename": "dictionary.pdf",

            "url": "https://www.princexml.com/samples/icelandic/dictionary.pdf",

            "mime": "application/pdf"
        }
    ]
}
```

### Example

- Linux

```bash
curl -X POST "http://localhost:8000/api/email-services?apikey=123456789" \
-H "Content-Type: application/json" \
-d '{
"to":"email@domain.com",
"subject":"HELLO WORLD FROM EMAIL",
"body":"BODY EMAIL"
}'
```

- Windows CMD

```bash
curl -X POST "http://localhost:8000/api/email-services?apikey=123456789" -H "Content-Type: application/json" -d "{\"to\":\"email@domain.com\",\"subject\":\"HELLO WORLD FROM EMAIL\",\"body\":\"BODY EMAIL\"}"
```
