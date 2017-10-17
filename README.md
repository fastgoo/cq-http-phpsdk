项目介绍
=======
Phalcon框架的基础项目架构 [线上地址](https://admin.fastgoo.net/vue/dist/index.html).

安装
------------
[官方扩展安装](https://phalconphp.com/zh/download/linux)
[开发扩展工具](https://github.com/phalcon/phalcon-devtools)
[中文扩展安装](http://www.iphalcon.cn/reference/install.html)

```bash
git clone https://github.com/jungege520/simple.phalcon.git phalcon
cd phalcon
composer install
```

依赖包
-------
1、[第三方支付](https://github.com/helei112g/payment)：集成微信、支付宝、银联所有支付类型。包含退款、查询、企业付款等扩展业务<br>

2、[极光推送](https://github.com/jpush/jpush-api-php-client)：集成集成极光推送3.5版本<br>

3、[七牛云存储](https://github.com/qiniu/php-sdk)：集成七牛云存储类，封装部分上传、认证、删除方法<br>

4、[PHP-JWT token认证](https://github.com/firebase/php-jwt)：集成JWT认证类，已封装加密解密的类方法<br>

5、[微信SDK](https://github.com/thenbsp/wechat)：微信的大部分常用的SDK都已封装，可查看WIKI文档<br>



项目结构
-------

```bash
project/
  app/
    config/       ---配置文件
      development/---开发环境配置文件
      testing/    ---测试环境配置文件
    controllers/  ---控制器
    helper/       ---公共方法
    library/      ---封装类
    migration/    ---数据库迁移文件
    models/       ---模型文件
    services/     ---业务类（存放业务操作方法）
    views/        ---视图
  public/         ---公共资源
    css/
    img/
    js/
  cache/          ---缓存文件（缓存，视图）
  log/            ---log日志（日志按日期分路径）
  resource/       ---文件资源路径（存储密钥、证书等等）
  vendor/         ---composer包管理
```

改动介绍
-------
1、分离 config.php 里面的数据库配置信息

2、添加公共配置文件common.php，包含接口输出方法、log日志初始、密码加密解密

3、改动loader.php文件，注册项目的命名空间

4、添加module.php、修改router.php的验证机制让其支持控制其的二级目录

5、改动services.php 全局注册数据库配置、公共配置、支付回调方法、redis缓存服务、

6、添加BaseController控制器，里面包含大部分的验证方法

7、Library添加支付宝支付、支付宝企业转账、接口签名、极光推送、JWT授权、支付通知、七牛存储、微信登录授权、微信支付、微信企业转账

8、Models添加Model基类，重写连表多数据查询、单表多数据查询、单表单数据查询、查询COUNT、SUM...！不影响Phalcon原Model的使用

9、services业务操作类

感言
-------

1、命名空间是个大坑，写方法的时候一定要注意命名空间的使用，一不小心就坑的你吐。

2、不要重复造轮子，多去找找有没有composer包，[点击传送门](https://packagist.org/)

3、多查看手册  [官方英文手册](https://docs.phalconphp.com/en/3.2) [3.0的中文手册](http://www.iphalcon.cn/)

4、记住多看手册，基本上大部分遇到的坑都会在手册查看，类的用法可以多查API [点击传送门](https://docs.phalconphp.com/en/3.2/api/index)



加入我们
-------
交流群：150237524

我的QQ：773729704 记得备注github

我的微信：huoniaojugege  记得备注github