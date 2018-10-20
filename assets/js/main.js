$(function () {

    if ('addEventListener' in window) {
        window.addEventListener('load', function () {
            document.body.className = document.body.className.replace(/\bis-preload\b/, '');
        });
        document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
    }

    MessageBox.error = function (msg) {
        Toastify({
            text: msg,
            duration: 3000,
            /*/destination: "https://github.com/apvarun/toastify-js",*/
            newWindow: true,
            close: true,
            gravity: "top", // `top` or `bottom`
            positionLeft: false, // `true` or `false`
            backgroundColor: "linear-gradient(to right, #FC1C5C, #FB7299)",
            className: "error",
        }).showToast();
    };
    MessageBox.info = function (msg) {
        Toastify({
            text: msg,
            duration: 3000,
            /*/destination: "https://github.com/apvarun/toastify-js",*/
            newWindow: true,
            close: true,
            gravity: "top", // `top` or `bottom`
            positionLeft: false, // `true` or `false`
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            className: "error",
        }).showToast();
    };
    MessageBox.success = function (msg) {
        Toastify({
            text: msg,
            duration: 3000,
            /*/destination: "https://github.com/apvarun/toastify-js",*/
            newWindow: true,
            close: true,
            gravity: "top", // `top` or `bottom`
            positionLeft: false, // `true` or `false`
            backgroundColor: "linear-gradient(to right, #20A821, #37B737)",
            className: "error",
        }).showToast();
    };


    let fileObj = $('#fileInput');
    let up_loading = $('.upload-loading');
    let distribute_progress = $('.distribute-progress');
    let image_list = $('.image-list');
    let image_list_box = $('#image-list-box');
    let fileObj_container = $('#fileobj-container');
    let select_loading = $('#select-loading');
    let resetBtn = $('#resetBtn');
    let pollingTimer = null;

    let setToken =  (tk) => {
        token = tk;
    };

    let getToken = () => {
        $.ajax({
            url: ajax_url,
            type: 'OPTIONS',
            async:false,
            success: ((data) => {
                if (data.code === 0){setToken(data.token);}
            }),
            error: ((err) => {
                setTimeout(() => {
                    MessageBox.error('请求失败...');
                },300);
            })
        });
    };

    let getImageUrls = (key) => {
        let k = key;
        let polling = () => {
            $.ajax({
                url: ajax_url,
                type: 'POST',
                data:{
                    'token': token,
                    'action': 'search',
                    'key': k
                },
                dataType: 'json',
                success: ((data) => {
                    setToken(data.token);
                    if (data.code === 0){
                        image_list.show();
                        select_loading.show();
                        if (data.urls.length > 0){
                            /*显示地址*/
                            let html = `<input type="text" value="${data.url}" /><p></p><span style="font-size: .7em;">分发链接</span>`;
                            for (let i = 0; i < data.urls.length; i++){
                                html += `<input type="text" value="${data.urls[i]}" />`;
                            }
                            image_list_box.html(html);
                        }
                        /*分发上传完成*/
                        if (data.done){
                            /*停止轮询*/
                            MessageBox.success('分发完成 ~');
                            distribute_progress.hide();
                            select_loading.hide();
                            resetBtn.show();
                            clearInterval(pollingTimer);
                        }
                    }
                }),
                error: ((err) => {
                    if (err.responseJSON.token){
                        setToken(err.responseJSON.token);
                    }
                    /*// setTimeout(() => {
                    //     MessageBox.error('请求失败...');
                    // },300);*/
                })
            });
        };
        pollingTimer = setInterval(() => {
            polling();
        },5000);
    };

    moe_upload = () => {
        let file = $('#fileInput')[0].files[0];
        if (file.size > upload_max_size * 1024 * 1024) {
            MessageBox.error('文件过大 ~');
        }else{
            getToken();
            let formDate = new FormData();
            formDate.append('token', token);
            formDate.append('action', 'upload');
            formDate.append('file', file);
            if (token !== ''){
                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    cache: false,
                    data: formDate,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    beforeSend: (() => {
                        MessageBox.info('正在上传中...');
                        fileObj.hide();
                        up_loading.show();
                    }),
                    success: ((data) => {
                        setToken(data.token);
                        up_loading.hide();
                        if (data.code === 0){
                            MessageBox.success(data.msg);
                            distribute_progress.show();
                            getImageUrls(data.key);
                        } else{
                            MessageBox.error(data.msg);
                        }
                    }),
                    error: ((err) => {
                        setToken(err.responseJSON.token);
                        setTimeout(() => {
                            MessageBox.error('请求失败...');
                            fileObj.show();
                            up_loading.hide();
                            again_input();
                        },300);
                    })
                });
            }else{
                MessageBox.error('请在刷新后重试...');
                setTimeout(() => {
                    window.location.reload();
                },3000);
            }
        }
    };

    reset = () => {
        image_list.hide();
        resetBtn.hide();
        image_list_box.html("");
        fileObj.show();
        getToken();
        /*停止轮询*/
        clearInterval(pollingTimer);
        again_input();
    };

    let again_input = () => {
        fileObj.remove();
        let file_input_html = `<input type="file" name="file" accept="image/*" id="fileInput" onchange="moe_upload()" />`;
        fileObj_container.append(file_input_html);
        fileObj = $('#fileInput');
    };

});