$('div.alert').delay(5000).slideUp();
$(document).ready(function() {
    $('.del_img_btn').click(function() {
        var url = "http://localhost/LaravelProject/admin/pro/delimg/";
        var _token = $("form[name='fEditProduct']").find("input[name='_token']").val();
        var srcImg = $(this).parent().find('img').attr('src');
        var idImg = $(this).parent().find('img').attr('idImg');
        var pos = $(this).parent().attr('id');
        $.ajax({
            url: url + idImg,
            type: 'GET',
            cache: false,
            data: {
                '_token': _token,
                'srcImg': srcImg,
                'idImg': idImg
            },
            success: function(flag) {
                if (flag == 'OK') {
                    $('#' + pos).remove();
                } else {
                    alert('Can not remove this image!!');
                }
            }
        });
    });
});