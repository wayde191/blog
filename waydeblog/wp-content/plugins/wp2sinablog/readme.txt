=== WP2Sinablog ===
Contributors: Starhai
Donate link: http://wpto.starhai.net/
Tags: sinablog,wp2sinablog,新浪博客,同步发布,sina,新浪
Requires at least: 2.9
Tested up to: 3.2
Stable tag: 2.0.1

同步发表 WordPress 博客日志到 新浪博客,初次安装必须设置后才能使用。

== Description ==

Version 2.0.1 功能

1。支持将Wordpress中文章链接发布到新浪博客，并可选原文链接显示的位置。

2。不支持将Wordpress中私密(private)文章发布到新浪博客(未经严格测试）。

3。解决了以往同步到新浪博客排版混乱的问题。


== Installation ==

1. 上传 `wp2sinablog.php`  `class-wp2sinablog.php`到 `/wp-content/plugins/` 目录

2. 在Wordpress后台控制面板"插件(Plugins)"菜单下激活wp2sinablog插件

3. 在Wordpress后台控制面板"配置(Settings)->WP2Sinablog"菜单下设置插件的必须信息。（只有经过设置，插件才能正常使用）

== Frequently Asked Questions ==

= 1.为何保存设置后，发布文件目录设置中单选按钮没有选中 =

如果你是第一次保存（或更改一个新的）新浪博客的用户名/密码，可能会出现此问题。您只需要选择类别后再按一次保存即可。


= 2.为何设置用户名和密码的时候老提示新浪用户名和密码错误，而登陆新浪是没错的 =

首先请检查您的输入是否正确。如输入时正确的，出现错误 `发布文件目录设置 尝试登录新浪博客失败，请检查用户名/密码是否正确!  ` 可能是设置用户名时输入了中文用户名，请更换成您的新浪邮箱来填写`新浪博客的登录名`。

如我的新浪博客用户名为`星海茫茫`，对应的新浪邮箱为`aofa198@sina.com`,在插件设置`新浪博客的登录名`时不能填写`星海茫茫`，应填写`aofa198@sina.com`.

== Changelog ==

= 2.0.1 =

Version 2.0.1  解决了同步后的排版问题。

* 不再采用Curl函数，改使用IXR函数库，更利于安装使用

* 如果提示找不到class-IXR.php，请将插件第一句中`ABSPATH.'`更改为你的Wordpress安装的绝对目录。

= 1.0.8 =

Version 1.0.8  重新写过代码，更改了发送到新浪博客的方式

* 不再采用Curl函数，改使用IXR函数库，更利于安装使用

* 如果提示找不到class-IXR.php，请将插件第一句中`ABSPATH.'`更改为你的Wordpress安装的绝对目录。

= 1.0.7 =

Version 1.0.7 可能存在不能发送文章到新浪私密博客的问题，请慎重选择发送私密博客选项。

* 修正部分主机关于curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);错误提示

* 解决部分主机下 WLW 不能发布文章的问题

= 1.0.6 =

* 解决目录读取不全的问题

* 修正部分主机wp2sinablog.php on line 109，wp2sinablog.php on line 90的错误提示

* 解决部分主机下 WLW 不能发布文章的问题

= 1.0.5 =

为解决插件不能使用，紧急放出Beta版。Version 1.0.5并未经过严格测试，如有bug请告知。不会对博客和新浪造成损害，但可能会出现私密博客同步到新浪

= 版本 1.0.4 =

* 增加将Wordpress中文章的标签发布到新浪博客

* 增加6个新的新浪排行榜选项，分别为星座、时尚、休闲、美食、育儿、教育

= 版本 1.0.3 =

* 增加将Wordpress中文章链接发布到新浪博客，并可选原文链接显示的位置

= 版本 1.0.2 =

* 增加将Wordpress中文章以`私密博客`方式发布到新浪博客

= 版本 1.0.1 =

* 在配置选项里增加服务器cURL库的判断，不满足插件运行条件，提示退出

* 修正新发布文章时，WLW（Window Live Writer）错误提示



== Upgrade Notice ==

= 2.0.1 =

Version 2.0.1  解决了同步后的排版问题。

* 不再采用Curl函数，改使用IXR函数库，更利于安装使用

* 如果提示找不到class-IXR.php，请将插件第一句中`ABSPATH.'`更改为你的Wordpress安装的绝对目录。


= 1.0.8 =

Version 1.0.8  重新写过代码，更改了发送到新浪博客的方式。

* 不再采用Curl函数，改使用IXR函数库，更利于安装使用

* 如果提示找不到class-IXR.php，请将插件第一句中`ABSPATH.'`更改为你的Wordpress安装的绝对目录。


= 1.0.7 =

Version 1.0.7 可能存在不能发送文章到新浪私密博客的问题，请慎重选择发送私密博客选项。

* 修正部分主机关于curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);错误提示

* 解决部分主机下 WLW 不能发布文章的问题

= 1.0.6 =

Version 1.0.6 可能存在不能发送文章到新浪私密博客的问题，请慎重选择发送私密博客选项。

* 解决目录读取不全的问题

* 修正部分主机wp2sinablog.php on line 109，wp2sinablog.php on line 90的错误提示

* 解决部分主机下 WLW 不能发布文章的问题

= 1.0.5 =

为解决插件不能使用，紧急放出Beta版。Version 1.0.5并未经过严格测试，如有bug请告知。不会对博客和新浪造成损害，但可能会出现私密博客同步到新浪

= 1.0.4 =

* 增加将Wordpress中文章的标签发布到新浪博客

* 增加6个新的新浪排行榜选项，分别为星座、时尚、休闲、美食、育儿、教育

= 1.0.3 =

增加将Wordpress中文章链接发布到新浪博客，并可选原文链接显示的位置

= 1.0.2 =

增加将Wordpress中文章以`私密博客`方式发布到新浪博客

= 1.0.1 =

修正新发布文章时，WLW（window live writer）错误提示，全面支持WLW（Window Live Writer）发布文章.