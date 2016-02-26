/**
 * Created by sakilu on 15/9/17.
 */
var height = $(window).height();
var f_h = height / 10;
var loading = '<div style="height:' + height + 'px; width:100%;">'
    + '<div class="spinner" style="top:' + f_h + 'px;position:relative;">'
    + '<div class="rect1"></div>'
    + '  <div class="rect2"></div>'
    + '  <div class="rect3"></div>'
    + '  <div class="rect4"></div>'
    + '  <div class="rect5"></div>'
    + '</div>';
+'</div>';

function ajax_load(url, content_id, msg) {
    var content = $('#' + content_id);
    $.ajax({
        url: url, // form action url
        cache: false,
        dataType: "html",
        beforeSend: function () {
            content.empty();
            content.html(loading); // change submit button text
        },
        success: function (data) {
            if (data !== '') {
                content.html(data).fadeIn(); // fade in response data
                if (msg) {
                    notify('訊息', msg, 'success', 'stack_top_right');
                }
            }
        },
        error: function (e) {
            content.html(e.responseText); // change submit button text
        }
    });
}

function ajax_remove(url, content_id, remove_url, msg) {
    if (confirm("確定要刪除?")) {
        $.ajax({
            url: remove_url, // form action url
            type: 'GET', // form submit method get/post
            dataType: 'html', // request type html/json/xml
            cache: false,
            beforeSend: function () {
                $.blockUI({message: null});
            },
            success: function (id) {
                $.unblockUI();
                ajax_load(url, content_id, msg);
            },
            error: function (e) {
                $.unblockUI();
                alert(e.responseText);
            }
        });
    }
}

function form_submit_and_go(form_id, alert_panel_id, content_id, msg, url) {
    var form = $('#' + form_id);
    var alert_panel = $('#' + alert_panel_id);
    var formdata = false;
    if (CKEDITOR) {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
    }
    if (window.FormData) {
        formdata = new FormData(form[0]);
    }
    $.ajax({
        url: form.attr('action'), // form action url
        type: 'POST', // form submit method get/post
        dataType: 'html', // request type html/json/xml
        data: formdata ? formdata : form.serialize(), // serialize form data
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $.blockUI({message: null});
        },
        success: function () {
            ajax_load(url, content_id, msg);
            $.unblockUI();
        },
        error: function (e) {
            $.unblockUI();
            alert_panel.html(e.responseText); // change submit button text
            $(alert_panel).closest('.alert').show();
        }
    });

}

function form_submit(form_id, alert_panel_id, content_id, msg) {
    var form = $('#' + form_id);
    var alert_panel = $('#' + alert_panel_id);
    var formdata = false;
    if (CKEDITOR) {
        for (instance in CKEDITOR.instances) {
            console.log(instance);
            CKEDITOR.instances[instance].updateElement();
        }
    }
    if (window.FormData) {
        formdata = new FormData(form[0]);
    }
    $.ajax({
        url: form.attr('action'), // form action url
        type: 'POST', // form submit method get/post
        dataType: 'html', // request type html/json/xml
        data: formdata ? formdata : form.serialize(), // serialize form data
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $.blockUI({message: null});
        },
        success: function (url) {
            $.unblockUI();
            if (content_id) {
                ajax_load(url, content_id, msg);
            } else {
                notify('訊息', msg, 'success', 'stack_bar_top');
            }
        },
        error: function (e) {
            $.unblockUI();
            alert_panel.html(e.responseText); // change submit button text
            $(alert_panel).closest('.alert').show();
        }
    });
}


function notify(title, msg, noteStyle, noteStack) {
    var Stacks = {
        stack_top_right: {
            "dir1": "down",
            "dir2": "left",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
        },
        stack_top_left: {
            "dir1": "down",
            "dir2": "right",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
        },
        stack_bottom_left: {
            "dir1": "right",
            "dir2": "up",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
        },
        stack_bottom_right: {
            "dir1": "left",
            "dir2": "up",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
        },
        stack_bar_top: {
            "dir1": "down",
            "dir2": "right",
            "push": "top",
            "spacing1": 0,
            "spacing2": 0
        },
        stack_bar_bottom: {
            "dir1": "up",
            "dir2": "right",
            "spacing1": 0,
            "spacing2": 0
        },
        stack_context: {
            "dir1": "down",
            "dir2": "left",
            "context": $("#stack-context")
        }
    };
    // If notification stack or opacity is not defined set a default
    var noteStack = noteStack ? noteStack : "stack_top_right";

    // We modify the width option if the selected stack is a fullwidth style
    function findWidth() {
        if (noteStack == "stack_bar_top") {
            return "100%";
        }
        if (noteStack == "stack_bar_bottom") {
            return "70%";
        } else {
            return "290px";
        }
    }

    new PNotify({
        title: title,
        text: msg,
        shadow: true,
        opacity: 0.8,
        addclass: noteStack,
        type: noteStyle,
        stack: Stacks[noteStack],
        width: findWidth(),
        delay: 3000,
        icon: false
    });
}

function admin_prompt(msg, title, callback) {
    $('#prompt-form-title').html(title);
    $('#prompt-form-help').html(msg);

    $('#prompt-form-submit').off();
    $.magnificPopup.open({
        removalDelay: 500,
        items: {
            src: '#prompt-form',
            type: 'inline'
        },
        callbacks: {
            beforeOpen: function (e) {
                this.st.mainClass = 'mfp-zoomIn';
            }
        },
        modal: true
    });

    var magnificPopup = $.magnificPopup.instance;
    $("#prompt-form-submit").on("click", function () {
        var val = $('#prompt-form-text').val();
        if (!$.trim(val).length) {
            callback(magnificPopup, null);
        } else {
            callback(magnificPopup, $('#prompt-form-text').val());
        }
    });
}

function prompt_cancel() {
    $.magnificPopup.close();
}

function printpage(myDiv) {

    //隱藏不列印的元件(class="noprint")
    $('.noprint').hide();

    //建立網頁複本
    var $copyhtml = $("html").clone();
    //複製css link
    var $copycss = $copyhtml.find('head').find('link');
    //複製css style
    var styles = $copyhtml.find('head').html().match(/<style.*?>[\s\S]*?<\/style>/ig);
    //清空head，填入css
    $copyhtml.find('head').empty().append($copycss).append(styles);
    //移除script
    $copyhtml.find('script').remove();
    //複本中替換body為目標div
    $copyhtml.find('body').html($copyhtml.find('#' + myDiv).html());
    //IE瀏覽器第一次載入時refresh頁面，確保完整套用樣式
    $copyhtml.find('body').append(
        '<script>'
        + 'if(!window.location.hash && navigator.userAgent.match("MSIE")) {'
        + 'window.location = window.location + \'#loaded\';window.location.reload(); } '
        + 'else { window.print(); setTimeout("window.close();",100); } '
        + '<\/script>'
    );

    //開新視窗，寫入HTML語法
    var printPage = window.open("", "printPage");
    printPage.document.open();
    printPage.document.write("<html><head>");
    printPage.document.write($copyhtml.find('head').html());
    printPage.document.write("</head><body>");
    printPage.document.write($copyhtml.find('body').html());
    printPage.document.close("</body></HTML>");

    //復原隱藏的元件
    $('.noprint').show();

    return false;
}