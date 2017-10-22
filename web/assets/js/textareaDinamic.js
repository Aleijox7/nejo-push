$(document).ready(function () {

    $('textarea').each(function () {
        //$(this).css('height','120px !important');
        //h(this);
        this.rows=5;
    }).on('input', function () {
        if (this.scrollHeight < 200) {

            h(this);
        } else {
            $(this).css('overflow-y', 'visible');
        }
    });
});
function h(e) {
    $(e).css({'height': 'auto', 'overflow-y': 'hidden'}).height(e.scrollHeight);
}
