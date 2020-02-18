# URL Shortener

Shorten long urls

| [English](README.md) | [简体中文](README_zh.md) |

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


## Requirements
  - Redis
  - Php (>=5.1)
  - Mysql (>=5.7.8, or MariaDB >=10.2)
  - Apache (with Rewrite module enabled)

## Setup
Want to deploy this service quickly? Try out this [one click setup](https://github.com/QuickDeploy/url-shortener) 
in [QuickDeploy](https://github.com/QuickDeploy/).

  - Install requirements
  - Download the latest [release](https://github.com/newnius/short-link/releases), unzip the tarball
  - Rename `config-sample.inc.php` to `config.inc.php`
  - Customize `config.inc.php` & `static/config.js`
  - Run `install.php` & __Remove__ `install.php`
  
#### Configuration Options

| Option | Description |
| --- | --- |
| DB_HOST | Mysql host, normally localhost |
| DB_PORT | Mysql port, normally 3306 |
| DB_NAME | Mysql database name |
| DB_USER | Mysql user |
| DB_PASSWORD | Mysql password |
| REDIS_HOST | Redis host, normally localhost |
| REDIS_PORT| Redis listen port, normally 6379 |
| BASE_URL | Base URL of your site |
| OAUTH_CLIENT_ID | ClientID |
| OAUTH_CLIENT_SECRET | ClientSecret |

If you want to run this service not only on localhost, it is required to update the OAuth properties
`OAUTH_CLIENT_ID` and `OAUTH_CLIENT_SECRET`.

To get your own configuration, login to [QuickAuth](https://quickauth.newnius.com/) and register for an account.

After login, visit `Sites` > `Add` , and add your server ip / domain (without `http://`, `/` or `sub dir`)

Click `View`, you can see the `ClientID` and `ClientSecret`.

The OAuth related functions are located at `auth.php`, `user.logic.php`.
