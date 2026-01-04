$(document).ready(function () {
  // 1. CẤU HÌNH API GHN
  const token = "9e958cee-c146-11f0-a621-f2a9392e54c8"; // Token GHN của bạn
  const headers = {
    "Content-Type": "application/json",
    Token: token,
  };

  // 2. LOAD TỈNH/THÀNH PHỐ KHI VÀO TRANG
  $.ajax({
    url: "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province",
    headers: headers,
    method: "GET",
    success: function (response) {
      if (response.code === 200) {
        $.each(response.data, function (index, item) {
          $("#province").append(
            `<option value="${item.ProvinceID}">${item.ProvinceName}</option>`
          );
        });
      }
    },
  });

  // 3. KHI CHỌN TỈNH -> LOAD QUẬN/HUYỆN
  $("#province").change(function () {
    var provinceId = $(this).val();
    var provinceName = $(this).find("option:selected").text();

    // Lưu tên Tỉnh để hiển thị
    $("#city_text").val(provinceName);

    // Reset Quận/Phường
    $("#district").html('<option value="0">Quận/Huyện</option>');
    $("#ward").html('<option value="0">Phường/Xã</option>');

    if (provinceId == 0) return;

    $.ajax({
      url: "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district",
      headers: headers,
      method: "POST",
      data: JSON.stringify({ province_id: parseInt(provinceId) }),
      success: function (response) {
        if (response.code === 200) {
          $.each(response.data, function (index, item) {
            $("#district").append(
              `<option value="${item.DistrictID}">${item.DistrictName}</option>`
            );
          });
        }
      },
    });
  });

  // 4. KHI CHỌN QUẬN -> LOAD PHƯỜNG/XÃ & LƯU DISTRICT_ID
  $("#district").change(function () {
    var districtId = $(this).val();
    var districtName = $(this).find("option:selected").text();

    // [QUAN TRỌNG] Lưu ID Quận vào input hidden để gửi Server
    $("#district_id").val(districtId);
    $("#district_text").val(districtName);

    // Reset Phường
    $("#ward").html('<option value="0">Phường/Xã</option>');

    if (districtId == 0) return;

    $.ajax({
      url:
        "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id=" +
        districtId,
      headers: headers,
      method: "GET",
      success: function (response) {
        if (response.code === 200) {
          $.each(response.data, function (index, item) {
            // GHN dùng WardCode (string), không phải ID (int)
            $("#ward").append(
              `<option value="${item.WardCode}">${item.WardName}</option>`
            );
          });
        }
      },
    });
  });

  // 5. KHI CHỌN PHƯỜNG -> LƯU WARD_CODE
  $("#ward").change(function () {
    var wardCode = $(this).val();
    var wardName = $(this).find("option:selected").text();

    // [QUAN TRỌNG] Lưu WardCode vào input hidden để sửa lỗi "Thiếu thông tin to_ward_code"
    $("#ward_code").val(wardCode);
    $("#ward_text").val(wardName);
  });
});
