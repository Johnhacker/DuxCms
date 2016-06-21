#DuxCMS 3

DuxCms3.0采用自主框架开发,具有轻便已开发高定制化得优点,目前进入beta测试期,现有功能逐步不定期完善中...

QQ群: 131331864

为了您的问题能尽快解决和群主的身心健康,有bug请提交在issues中,切勿在Q群刷屏,提问时请带上您的运行环境以便问题能够快速解决!


需要伪静态支持,规则如下

RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f

RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^(.*)$ index.php [QSA,L]

