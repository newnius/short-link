# URL Shortener

网址缩短、推广链接效果统计、用户无感知切换链接

| [English](README.md) | [简体中文](README_zh.md) |

## 特性
  - 自定义短网址
  - 生成对应的图片二维码
  - 支持设置起止有效期
  - 支持创建后修改原始链接
  - 支持暂停/恢复
  - 形式丰富的分析统计功能
  - 支持链接导出
  - 去除容易混淆的短网址字符
  - 使用307状态码保留请求协议，如`POST`等
  - 管理员功能


## 环境需求
  - Redis
  - Php (>=5.1)
  - Mysql (>=5.7.8, or MariaDB >=10.2)
  - Apache (开启了 Rewrite 模块)

## 部署

  - 安装必要的依赖
  - 下载[发布版本](https://github.com/newnius/short-link/releases)，并将文件解压作为网站根目录
  - 复制文件 `config-sample.inc.php` 至 `config.inc.php`
  - 修改配置文件 `config.inc.php` 以及 `static/config.js` 中的对应参数
  - 访问 `install.php` 页面执行初始化操作（为了安全起见，建议删除`install.php`）

除了在物理机上部署之外，你也可以选择使用[基于docker部署](https://github.com/QuickDeploy/url-shortener)

#### 可配置项

| 参数 | 说明 |
| --- | --- |
| DB_HOST | Mysql 数据库的host，一般是是localhost |
| DB_PORT | Mysql 数据库的端口，一般是3306 |
| DB_NAME | Mysql 数据库的库名，这需要你自己在数据库里新建一个，名字随意，一致就行 |
| DB_USER | Mysql数据库连接用的用户名 |
| DB_PASSWORD | Mysql数据库连接密码 |
| REDIS_HOST | Redis数据库host，一般是localhost |
| REDIS_PORT| Redis数据库监听端口，一般是6379 |
| BASE_URL | 网站根网址，参考默认值改 |
| OAUTH_CLIENT_ID | Oauth登陆id |
| OAUTH_CLIENT_SECRET | Oauth登陆key |

#### OAuth配置
如果需要部署在本机以外的环境（非127.0.0.1），需要配置OAuth，包括`OAUTH_CLIENT_ID` 和 `OAUTH_CLIENT_SECRET`两项。

  1. 访问网站[QuickAuth](https://quickauth.newnius.com/)，注册并登录，
  2. 选择 `Sites` > `Add`
  3. 根据你的访问方式填写网站域名或ip地址（不包括 `http://`、`/`，以及路径）
  4. 点击`View`，找到`ClientID`和`ClientSecret`

*OAuth相关的实现在`auth.php`和`user.logic.php`文件中，如果需要接入其他第三方登录，可以自行参考实现*
