# 聚合床图


![PHP from Travis config](https://img.shields.io/travis/php-v/symfony/symfony.svg)
![Packagist](https://img.shields.io/packagist/l/doctrine/orm.svg)


### 简介

只需上传一次图片

后台程序会分发到各个床图

将获取的图片链接存到数据库

并且取一条存到memcached进行缓存

后续访问直接返回缓存中的图片url

对于有可能出现的图片链接失效

可以加定期任务来去除失效的图片链接、更新缓存

这样图片就不会丢失了  除非下面的床图全挂了

### 特点

用了百度的色情api检测

可以公用 防止别人上传色情图片

### 已知问题

因为写的时候没多想

用了php跑的 长时间跑脚本

后来发现不是很稳定 ~

### 全部床图

- [微博床图](不说了) 

- [搜狗床图](http://pic.sogou.com)

- [Catbox](https://catbox.moe)

- [Imgvim](https://img.vim-cn.com)

- [牛图网](https://www.niupic.com)

- [OOXX](https://ooxx.ooo)

- [SMMS](https://sm.ms/doc/)

- [爱信息](https://tu.aixinxi.net)

- [UploadCC](https://upload.cc/)

- [Ouliu.net](https://upload.ouliu.net)

- [秒速5厘米](https://miao.su)

- [路过床图](https://imgchr.com)

- [Z4A图床](https://www.z4a.net)

- [A.photo](https://a.photo)

- [MoeTu](https://moetu.org)

### 配置

###### Nginx 重写规则

>
>if (!-d $request_filename) {
>
>   rewrite ^/(.*)/(.*)/*$ /index.php?action=$1&args=$2 last;
>
>   break;
>
>}
>


apache 没用过...

### 如图

![](https://ww1.sinaimg.cn/large/005YhI8igy1fwey2rbrycj31hc0u0b2a)

![](https://ww1.sinaimg.cn/large/005YhI8igy1fwey38e5pjj31hc0t44qq)
