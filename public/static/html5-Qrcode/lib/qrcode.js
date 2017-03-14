(function($) {
    var Qrcode = function(tempBtn) {
        var _this_ = this;
        var isWeiboWebView = /__weibo__/.test(navigator.userAgent);

        if (isWeiboWebView) {
            if (window.WeiboJSBridge) {
                _this_.bridgeReady(tempBtn);
            } else {
                document.addEventListener('WeiboJSBridgeReady', function() {
                    _this_.bridgeReady(tempBtn);
                });
            }
        } else {
            _this_.nativeReady(tempBtn);
        }
    };

    Qrcode.prototype = {
        nativeReady: function(tempBtn) {
            $('[node-type=jsbridge]',tempBtn).on('click',function(e){
                e.stopPropagation();
            });

            $(tempBtn).bind('click',function(e){
                $(this).find('input[node-type=jsbridge]').trigger('click');
            });

            $(tempBtn).bind('change', this.getImgFile);
        },
        bridgeReady: function(tempBtn) {
            $(tempBtn).bind('click', this.weiBoBridge);
        },
        weiBoBridge: function() {
            window.WeiboJSBridge.invoke('scanQRCode', null, function(params) {
                //得到扫码的结果
                $('.result-qrcode').append(params.result + '<br/>');
            });
        },
        getImgFile: function() {
            $('.result-qrcode').empty();
            var _this_ = this;
            var inputDom = $(this).find('input[node-type=jsbridge]');
            console.log('Loging, inputDom: ', inputDom);
            if (inputDom.length === 0) {
                return;
            }
            var imgFile = inputDom[0].files;
            var oFile = imgFile[0];
            var oFReader = new FileReader();
            var rFilter = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;
            if (!rFilter.test(oFile.type)) {
                alert("选择正确的图片格式!");
                return;
            }

            if (imgFile.length === 0) {
                return;
            }

            oFReader.onload = function(oFREvent) {
                console.log('Scan oFREvent is:', oFREvent);

                qrcode.decode(oFREvent.target.result);
                qrcode.callback = function(data) {
                    //得到扫码的结果
                    console.log('Scan result is:', data);
                    $('.result-qrcode').empty();
                    $('.result-qrcode').append(data);
                };
            };

            oFReader.readAsDataURL(oFile);
        },
        destory: function() {
            $(tempBtn).off('click');
        }
    };

    Qrcode.init = function(tempBtn) {
        var _this_ = this;

        tempBtn.each(function() {
            new _this_($(this));
        });
    };
    window.Qrcode = Qrcode;

    
})(window.Zepto ? Zepto : jQuery);
