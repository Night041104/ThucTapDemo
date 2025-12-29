$(document).ready(function() {
    // 1. LẤY DANH SÁCH TỈNH THÀNH
    $.getJSON('https://esgoo.net/api-tinhthanh/1/0.htm', function(data_tinh) {
        if (data_tinh.error == 0) {
            $.each(data_tinh.data, function(key_tinh, val_tinh) {
                $("#province").append('<option value="' + val_tinh.id + '" data-name="' + val_tinh.full_name + '">' + val_tinh.full_name + '</option>');
            });
        }
    });

    // 2. KHI CHỌN TỈNH -> LẤY QUẬN HUYỆN
    $("#province").change(function() {
        var idtinh = $(this).val();
        var tenTinh = $(this).find('option:selected').data('name');
        $('#city_text').val(tenTinh); // Lưu tên tỉnh vào input hidden

        // Reset quận/huyện và xã/phường
        $("#district").html('<option value="0">Chọn Quận/Huyện</option>');
        $("#ward").html('<option value="0">Chọn Phường/Xã</option>');

        if (idtinh != 0) {
            $.getJSON('https://esgoo.net/api-tinhthanh/2/' + idtinh + '.htm', function(data_quan) {
                if (data_quan.error == 0) {
                    $.each(data_quan.data, function(key_quan, val_quan) {
                        $("#district").append('<option value="' + val_quan.id + '" data-name="' + val_quan.full_name + '">' + val_quan.full_name + '</option>');
                    });
                }
            });
        }
    });

    // 3. KHI CHỌN QUẬN -> LẤY PHƯỜNG XÃ
    $("#district").change(function() {
        var idquan = $(this).val();
        var tenQuan = $(this).find('option:selected').data('name');
        $('#district_text').val(tenQuan); // Lưu tên quận

        $("#ward").html('<option value="0">Chọn Phường Xã</option>');

        if (idquan != 0) {
            $.getJSON('https://esgoo.net/api-tinhthanh/3/' + idquan + '.htm', function(data_phuong) {
                if (data_phuong.error == 0) {
                    $.each(data_phuong.data, function(key_phuong, val_phuong) {
                        $("#ward").append('<option value="' + val_phuong.id + '" data-name="' + val_phuong.full_name + '">' + val_phuong.full_name + '</option>');
                    });
                }
            });
        }
    });

    // 4. KHI CHỌN PHƯỜNG -> LƯU TÊN
    $("#ward").change(function() {
        var tenPhuong = $(this).find('option:selected').data('name');
        $('#ward_text').val(tenPhuong);
    });
});