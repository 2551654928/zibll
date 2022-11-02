jQuery(document).ready(function () {
    var $ = jQuery;
    $('body').on('click', '.process-submit', function (e) {
        return confirm("确认处理此申请？");
    })
})