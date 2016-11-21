=== WP2HiBaidu ===
Contributors: Starhai
Donate link: http://starhai.net/
Tags: baidu,wp2hibadiu,百度空间,同步发布,百度
Requires at least: 2.7
Tested up to: 3.0.4
Stable tag: 1.0.4

== Description ==

同步发表 WordPress 博客日志到 百度空间,初次安装必须设置后才能使用。


Version 1.0 支持功能


1。支持选择发布到百度空间中文章的评论权限。

2。支持选择发布到百度空间中文章的访问权限。

3。支持选择发布到百度空间中文章的转载权限。

4。支持将Wordpress中文章链接发布到百度空间，并可选原文链接显示的位置。


Version 1.0 不支持功能

1。不支持将Wordpress中私密(private)文章发布到百度空间。

2。不支持自动获取百度空间的类别。


== Installation ==

1. 1.0.4版，上传 `wp2hibaidu.php`, `iconv.php`, `gb2312-utf8.table` 到 `/wp-content/plugins/` 目录

2. 在Wordpress后台控制面板"插件(Plugins)"菜单下激活wp2hibaidu插件

3. 在Wordpress后台控制面板"配置(Settings)->wp2hibaidu"菜单下设置插件的必须信息。（只有经过设置，插件才能正常使用）

== Frequently Asked Questions ==

= 1。如何填写百度空间的UR =

百度空间的UR为你的百度空间唯一标示符，如百度空间地址为`http://hi.baidu.com/%D0%C7%BA%A3%B2%A9%BF%CD`，则百度空间的URL应填写`%D0%C7%BA%A3%B2%A9%BF%CD`。
如百度空间地址为`http://hi.baidu.com/mesamis`，则百度空间的URL应填写`mesamis`

= 2。关于ICONV函数问题 =

如果您的服务器不支持ICONV函数，那么您将无法使用该插件。

== Changelog ==

= 1.0.4 =

修复百度空间升级，不能同步的问题。

= 版本 1.0.2 =

* 修复因百度空间升级原来版本无法使用的问题

* 修复文章中有繁体字和其他特殊字符无法发送到百度空间的问题


= 版本 1.0.1 =

* 解决有些虚拟主机不支持ICONV函数



== Upgrade Notice ==

= 1.0.4 =

* 修复百度空间升级，不能同步的问题。

* 修正发送到百度空间后文章缩成一团的问题。

= 1.0.3 =

非重要升级，您可以不升级。

* 修改插件主页地址

= 1.0.2 =

* 修复因百度空间升级原来版本无法使用的问题

* 修复文章中有繁体字和其他特殊字符无法发送到百度空间的问题