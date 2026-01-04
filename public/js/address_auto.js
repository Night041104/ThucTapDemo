$(document).ready(function () {
  // 1. LẤY DANH SÁCH TỈNH THÀNH KHI VÀO TRANG
  $.getJSON("https://esgoo.net/api-tinhthanh/1/0.htm", function (data_tinh) {
    if (data_tinh.error == 0) {
      $.each(data_tinh.data, function (key_tinh, val_tinh) {
        // Thêm option vào thẻ select có id="province"
        $("#province").append(
          '<option value="' +
            val_tinh.id +
            '">' +
            val_tinh.full_name +
            "</option>"
        );
      });
    }
  });

  // 2. KHI CHỌN TỈNH -> LẤY QUẬN HUYỆN
  $("#province").change(function () {
    var idtinh = $(this).val();
    var tenTinh = $(this).find("option:selected").text(); // Lấy tên hiển thị
    $("#city_text").val(tenTinh); // Lưu vào input hidden

    // Gọi API lấy quận huyện
    $.getJSON(
      "https://esgoo.net/api-tinhthanh/2/" + idtinh + ".htm",
      function (data_quan) {
        if (data_quan.error == 0) {
          // Reset quận và xã
          $("#district").html('<option value="0">Quận/Huyện</option>');
          $("#ward").html('<option value="0">Phường/Xã</option>');

          $.each(data_quan.data, function (key_quan, val_quan) {
            $("#district").append(
              '<option value="' +
                val_quan.id +
                '">' +
                val_quan.full_name +
                "</option>"
            );
          });
        }
      }
    );
  });

  // 3. KHI CHỌN QUẬN -> LẤY PHƯỜNG XÃ
  $("#district").change(function () {
    var idquan = $(this).val();
    var tenQuan = $(this).find("option:selected").text();
    $("#district_text").val(tenQuan);

    $.getJSON(
      "https://esgoo.net/api-tinhthanh/3/" + idquan + ".htm",
      function (data_phuong) {
        if (data_phuong.error == 0) {
          $("#ward").html('<option value="0">Phường/Xã</option>');
          $.each(data_phuong.data, function (key_phuong, val_phuong) {
            $("#ward").append(
              '<option value="' +
                val_phuong.id +
                '">' +
                val_phuong.full_name +
                "</option>"
            );
          });
        }
      }
    );
  });

  // 4. KHI CHỌN PHƯỜNG -> LƯU TÊN
  $("#ward").change(function () {
    var tenPhuong = $(this).find("option:selected").text();
    $("#ward_text").val(tenPhuong);
  });
});
