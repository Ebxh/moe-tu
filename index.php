<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * ------------Oooo---
 * -----------(----)---
 * ------------)--/----
 * ------------(_/-
 * ----oooO----
 * ----(---)----
 * -----\--(--
 * ------\_)-
 * ----
 *     © 2018/8/22 Mex.
 *     author : Yprisoner
 *     email : yyprisoner@gmail.com
 *                            ------
 *    「 涙の雨が頬をたたくたびに美しく 」
 */
if (version_compare(PHP_VERSION, '7.0.0', '<')) die('PHP Version > 7.0 !');

require_once 'app/common.php';
$check_client_ip = true;
check_dir();
check_client_ip();
require_once 'app/route.php';
?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <title>聚合床图</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js@1.3.0/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome-animation@0.2.0/dist/font-awesome-animation.min.css">
    <link rel="stylesheet" href="//cdnjs.loli.net/ajax/libs/mdui/0.4.1/css/mdui.min.css">
    <link rel="stylesheet" href="<?php asset('css/main.css'); ?>"/>
    <noscript>
        <link rel="stylesheet" href="<?php asset('css/noscript.css'); ?>"/>
    </noscript>
</head>
<body class="is-preload">
<!-- Wrapper -->
<div id="wrapper">

    <?php if ($check_client_ip): ?>
    <!-- Main -->
    <section id="main">
        <header>
            <br>
            <p> - 聚合床图 点击上传 - </p>
            <p style="font-size: .8em">每日上传图片限量 2000 / 单张最大 <?php echo upload_max_size; ?> MB</p>
        </header>
        <br>
        <div class="upload-loading" style="display: none">
            <i class="fa fa-spinner faa-spin animated"></i>
            <p>上传中...</p>
        </div>
        <div class="distribute-progress" style="padding: 0 3em;display: none;">
            <div class="mdui-progress">
                <div class="mdui-progress-indeterminate"></div>
            </div>
            <br>
            <p>后台分发中...</p>
        </div>
        <div class="field image-list" style="padding: 0 2em; display: none">
            <label for="imageInput"></label>
            <div id="image-list-box"></div>
            <div id="select-loading" style="display: none">
                <p></p>
                <i class="fa fa-spinner faa-spin animated"></i>
                <p style="font-size: .7em">获取分发链接中...</p>
            </div>
            <br>
            <a id="resetBtn" style="display: none" href="javascript:reset();" class="button">继续上传</a>
        </div>
        <br>
        <form method="post" action="javascript:;">
            <div class="fields">
                <div class="field" id="fileobj-container">
                    <input type="file" name="file" accept="image/*" id="fileInput" onchange="moe_upload()" />
                </div>
            </div>
        </form>
        <footer>
            <ul class="icons">
                <li><a href="#" class="fa-home">Home</a></li>
                <li><a target="_blank" href="https://twitter.com/yyprisoner" class="fa-twitter">Twitter</a></li>
            </ul>
            <i style="font-size: .7em">本站已开启色情检测 不要上传色情图片</i>
        </footer>
    </section>
    <?php else: ?>
        <!-- Main -->
        <section id="main">
            <header>
                <br>
                <p> - IP 被封禁 - </p>
                <p style="font-size: .8em">你的IP (<?php echo get_client_ip(); ?>) 被封禁</p>
            </header>
            <footer>
                <ul >
                    <p style="font-size: .8em">详情请联系站长 QQ: 728828299</p>
                </ul>
                <i style="font-size: .7em">本站已开启色情检测，一旦上传色情图片，会立即封禁IP段，请不要作死 !</i>
            </footer>
        </section>
    <?php endif; ?>
    <!-- Footer -->
    <footer id="footer">
        <ul class="copyright">
            <li>&copy; 2018 Yprisoner</li>
        </ul>
    </footer>

</div>

<?php if ($check_client_ip): ?>
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js@1.3.0/src/toastify.min.js"></script>
<script src="//cdnjs.loli.net/ajax/libs/mdui/0.4.1/js/mdui.min.js"></script>
<script>let token = '', upload_max_size = <?php echo upload_max_size; ?> , ajax_url = "<?php echo get_url('/app/moe_ajax.php'); ?>", MessageBox = {};</script>
<script src="<?php asset('js/main.min.js'); ?>"></script>
<?php else: ?>
    <script>
        if ('addEventListener' in window) {
            window.addEventListener('load', function () {
                document.body.className = document.body.className.replace(/\bis-preload\b/, '');
            });
            document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
        }
    </script>
<?php endif; ?>
</body>
</html>