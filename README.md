# Link Shortener

Shorten long urls

## Requirements
  - Redis
  - Php (>=5.1)
  - Mysql

## Setup
  - Install requirements
  - Rename `config-sample.inc.php` to `config.inc.php`
  - Customize `config.inc.php` & `static/config.js`
  - Run `install.php` & __Remove__ `install.php`

## features
  - Customized short url
  - Generate related QR code
  - Support expiration time
  - Support available time
  - Update after created
  - Pause & Resume
  - Various forms of visit log analytics
  - Able to export your short urls
  - Remove confusing chars (l, I etc.)
  - Use 307 status code to preserve request method
  - Block unhealthy short links

## Future Work
  - Use [ClickHouse](https://github.com/yandex/ClickHouse) to act as the query log system
  - Use [RabbitMQ](https://github.com/rabbitmq/rabbitmq-server) to log query async and thus speed up the query
