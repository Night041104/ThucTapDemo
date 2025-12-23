-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th12 22, 2025 lúc 09:11 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shop_mobile_test`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attributes`
--

CREATE TABLE `attributes` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `input_type` varchar(20) NOT NULL,
  `is_filterable` tinyint(1) DEFAULT 1,
  `is_customizable` tinyint(4) DEFAULT 0,
  `is_variant` tinyint(1) DEFAULT 0 COMMENT '1: Dùng để sinh biến thể'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `attributes`
--

INSERT INTO `attributes` (`id`, `code`, `name`, `input_type`, `is_filterable`, `is_customizable`, `is_variant`) VALUES
(1, 'color', 'Màu sắc', 'select', 1, 1, 1),
(2, 'rom', 'Dung lượng', 'select', 1, 0, 1),
(3, 'ram', 'Ram', 'select', 1, 0, 0),
(4, 'tansoquet', 'Tần số quét', 'select', 1, 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attribute_options`
--

CREATE TABLE `attribute_options` (
  `id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `attribute_options`
--

INSERT INTO `attribute_options` (`id`, `attribute_id`, `value`) VALUES
(1, 1, 'Tím'),
(2, 2, '128GB'),
(3, 3, '8GB'),
(4, 3, '16GB'),
(5, 3, '32GB'),
(6, 1, 'Đỏ'),
(7, 1, 'xanh'),
(8, 1, 'vàng'),
(9, 1, 'cam'),
(10, 1, 'lục'),
(11, 1, 'lam'),
(12, 1, 'chàm'),
(13, 2, '64GB'),
(14, 2, '256GB'),
(15, 2, '512GB'),
(16, 4, '60Hz'),
(17, 4, '120Hz'),
(18, 4, '144Hz'),
(19, 4, '165Hz');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `logo_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `logo_url`, `created_at`) VALUES
(1, 'Apple', 'apple', 'uploads/brands/1766224930_Apple-Logo.png', '2025-12-13 12:48:08'),
(2, 'Xiaomi', 'xiaomi', 'uploads/brands/1766224937_Xiaomi_logo_(2021-).svg.png', '2025-12-15 06:26:13'),
(8, 'HAVIT', 'havit', '', '2025-12-20 10:04:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `spec_template` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`spec_template`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `created_at`, `spec_template`) VALUES
(1, 'Điện thoại', 'dien-thoai', NULL, '2025-12-13 12:48:08', '[{\"group_name\":\"Thông tin hàng hóa\",\"items\":[{\"name\":\"Xuất xứ\",\"type\":\"text\"},{\"name\":\"Thời  điểm ra mắt\",\"type\":\"text\"},{\"name\":\"Thời  gian bảo hành\",\"type\":\"text\"}]},{\"group_name\":\"Thiết kế & Trọng lượng\",\"items\":[{\"name\":\"Kích thước\",\"type\":\"text\"},{\"name\":\"Trọng lượng sản phẩm\",\"type\":\"text\"},{\"name\":\"Chất liệu\",\"type\":\"text\"},{\"name\":\"Màu sắc\",\"type\":\"attribute\",\"attribute_id\":\"1\"}]},{\"group_name\":\"Bộ xử lý\",\"items\":[{\"name\":\"Phiên bản CPU\",\"type\":\"text\"},{\"name\":\"Loại CPU\",\"type\":\"text\"},{\"name\":\"CPU\",\"type\":\"text\"},{\"name\":\"Số nhân\",\"type\":\"text\"}]},{\"group_name\":\"Màn hình\",\"items\":[{\"name\":\"Kích thước màn hình\",\"type\":\"text\"},{\"name\":\"Công nghệ  màn hình\",\"type\":\"text\"},{\"name\":\"Chuẩn màn hình\",\"type\":\"text\"},{\"name\":\"Độ phân giải\",\"type\":\"text\"},{\"name\":\"Tần số quét\",\"type\":\"attribute\",\"attribute_id\":\"4\"}]},{\"group_name\":\"Lưu trữ\",\"items\":[{\"name\":\"Dung lượng (ROM)\",\"type\":\"attribute\",\"attribute_id\":\"2\"}]},{\"group_name\":\"RAM\",\"items\":[{\"name\":\"RAM\",\"type\":\"attribute\",\"attribute_id\":\"3\"}]}]'),
(2, 'Tai nghe', 'tai-nghe', NULL, '2025-12-15 06:21:36', '[{\"group_name\":\"Thông tin hàng hóa\",\"items\":[{\"name\":\"Model\",\"type\":\"text\"},{\"name\":\"Xuất xứ\",\"type\":\"text\"},{\"name\":\"Thời gian bảo hành\",\"type\":\"text\"}]},{\"group_name\":\"Thiết kế & Trọng lượng\",\"items\":[{\"name\":\"Trọng lượng sản phẩm\",\"type\":\"text\"},{\"name\":\"Màu sắc\",\"type\":\"attribute\",\"attribute_id\":\"1\"}]},{\"group_name\":\"Giao tiếp và kết nối\",\"items\":[{\"name\":\"bluetooth\",\"type\":\"text\"}]},{\"group_name\":\"Lưu trữ\",\"items\":[{\"name\":\"Dung luọng (ROM)\",\"type\":\"attribute\",\"attribute_id\":\"2\"}]},{\"group_name\":\"RAM\",\"items\":[{\"name\":\"RAM\",\"type\":\"attribute\",\"attribute_id\":\"3\"}]}]');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `category_brand`
--

CREATE TABLE `category_brand` (
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `category_brand`
--

INSERT INTO `category_brand` (`category_id`, `brand_id`) VALUES
(1, 1),
(1, 2),
(2, 2),
(2, 8);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'fixed',
  `value` int(11) NOT NULL DEFAULT 0,
  `min_order_amount` int(11) DEFAULT 0,
  `quantity` int(11) DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `value`, `min_order_amount`, `quantity`, `start_date`, `end_date`, `status`) VALUES
(1, 'GIAMNGAY50K', 'fixed', 50000, 200000, 100, '2023-01-01', '2025-12-31', 1),
(2, 'SALE10', 'percent', 10, 100000, 94, '2023-01-01', '2025-12-31', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_code` varchar(20) NOT NULL COMMENT 'Mã đơn hàng (VD: FBT2512...)',
  `user_id` char(36) DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `note` text DEFAULT NULL,
  `total_money` decimal(15,0) NOT NULL DEFAULT 0,
  `payment_method` varchar(50) NOT NULL DEFAULT 'COD',
  `coupon_code` varchar(50) DEFAULT NULL,
  `discount_money` int(11) DEFAULT 0,
  `status` tinyint(4) DEFAULT 1 COMMENT '1: Chờ xác nhận, 2: Đã xác nhận, 3: Đang giao, 4: Hoàn thành, 5: Hủy',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `user_id`, `fullname`, `email`, `phone`, `address`, `note`, `total_money`, `payment_method`, `coupon_code`, `discount_money`, `status`, `created_at`) VALUES
(1, 'FBT251220-C80A', NULL, 'nguyễn văn a', NULL, '0905093044', '123 nguyễn trãi', '', 77980000, 'COD', NULL, 0, 1, '2025-12-20 13:17:55'),
(2, 'FBT251221-5902', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 38990000, 'COD', NULL, 0, 1, '2025-12-21 08:13:34'),
(3, 'FBT251221-3A82', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 38990000, 'COD', NULL, 0, 1, '2025-12-21 08:32:31'),
(4, 'FBT251221-51FB', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 38990000, 'COD', NULL, 0, 1, '2025-12-21 08:37:21'),
(5, 'FBT251221-5A4C', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '1234567890', '123 nguyễn trãi', '', 38990000, 'COD', NULL, 0, 1, '2025-12-21 08:39:21'),
(6, 'FBT251221-6276', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 38990000, 'COD', NULL, 0, 1, '2025-12-21 08:43:06'),
(7, 'FBT251221-7C61', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 38990000, 'COD', NULL, 0, 1, '2025-12-21 08:44:51'),
(8, 'FBT251221-1878', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 38990000, 'COD', NULL, 0, 1, '2025-12-21 08:49:51'),
(9, 'FBT251221-B5E6', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 38990000, 'COD', NULL, 0, 1, '2025-12-21 09:01:26'),
(10, 'FBT251221-0966', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 38990000, 'VNPAY', NULL, 0, 2, '2025-12-21 09:20:48'),
(11, 'FBT251221-F317', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 38990000, 'VNPAY', NULL, 0, 2, '2025-12-21 09:27:16'),
(12, 'FBT251221-DE56', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 35091000, 'COD', 'SALE10', 3899000, 1, '2025-12-21 09:44:53'),
(13, 'FBT251221-8A3C', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 38990000, 'VNPAY', NULL, 0, 1, '2025-12-21 09:46:36'),
(14, 'FBT251221-ED39', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 35091000, 'VNPAY', 'SALE10', 3899000, 2, '2025-12-21 09:47:52'),
(15, 'FBT251221-DCC5', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 35091000, 'COD', 'SALE10', 3899000, 1, '2025-12-21 09:52:39'),
(16, 'FBT251221-F3B3', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 69282000, 'COD', 'SALE10', 7698000, 1, '2025-12-21 10:04:15'),
(17, 'FBT251221-AC90', NULL, 'nguyễn văn a', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 35091000, 'COD', 'SALE10', 3899000, 4, '2025-12-21 12:45:37'),
(18, 'FBT251222-F63A', '54f9aba5-906e-4045-bad0-90cf9448fa0c', 'C trần văn', 'loanthanh3210s1@gmail.com', '1234567890', '123 nguyễn trãi', '', 38990000, 'COD', NULL, 0, 1, '2025-12-22 06:16:19'),
(19, 'FBT251222-C45C', '54f9aba5-906e-4045-bad0-90cf9448fa0c', 'C trần văn', 'loanthanh3210s1@gmail.com', '123456789', '123 nguyễn trãi', '', 38990000, 'VNPAY', NULL, 0, 2, '2025-12-22 06:17:06'),
(20, 'FBT251222-0275', '822f9c1f-ed60-4c44-a3c7-889c308d4afc', 'e trần văn', 'loanthanh3210q@gmail.com', '123456789', '123 nguyễn trãi', '', 102573000, 'VNPAY', 'SALE10', 11397000, 2, '2025-12-22 06:36:31'),
(21, 'FBT251222-35BD', '822f9c1f-ed60-4c44-a3c7-889c308d4afc', 'e trần văn', 'loanthanh3210q@gmail.com', '123456789', '123 nguyễn trãi', '', 37990000, 'VNPAY', NULL, 0, 1, '2025-12-22 06:39:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `product_name` varchar(255) NOT NULL COMMENT 'Lưu cứng tên SP thời điểm mua',
  `price` decimal(15,0) NOT NULL COMMENT 'Lưu cứng giá lúc mua',
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`) VALUES
(1, 1, 107, 'Iphone 15 Promax 128GB', 38990000, 2),
(2, 2, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(3, 3, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(4, 4, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(5, 5, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(6, 6, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(7, 7, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(8, 8, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(9, 9, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(10, 10, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(11, 11, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(12, 12, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(13, 13, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(14, 14, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(15, 15, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(16, 16, 105, 'Iphone 15 Promax 128GB', 37990000, 1),
(17, 16, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(18, 17, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(19, 18, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(20, 19, 107, 'Iphone 15 Promax 128GB', 38990000, 1),
(21, 20, 105, 'Iphone 15 Promax 128GB', 37990000, 3),
(22, 21, 105, 'Iphone 15 Promax 128GB', 37990000, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` bigint(20) NOT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `sku` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `price` decimal(15,0) DEFAULT NULL,
  `market_price` decimal(15,0) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `thumbnail` varchar(500) DEFAULT NULL,
  `specs_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specs_json`)),
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `parent_id`, `sku`, `name`, `slug`, `category_id`, `brand_id`, `price`, `market_price`, `quantity`, `thumbnail`, `specs_json`, `status`, `created_at`) VALUES
(105, NULL, 'SP-2B38169E', 'Iphone 15 Promax 128GB', 'iphone-15-promax-128gb', 1, 2, 37990000, 40000000, 115, 'uploads/products/1766153219_thumb_samsung_galaxy_z_fold7_xam_2_599278fa70.webp', '[{\"group_name\":\"Thông tin hàng hóa\",\"items\":[{\"name\":\"Xuất xứ\",\"value\":\" Trung Quốc\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Thời  điểm ra mắt\",\"value\":\" 09\\/2025\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Thời  gian bảo hành\",\"value\":\" 24 tháng\",\"type\":\"text\",\"attr_id\":\"\"}]},{\"group_name\":\"Thiết kế & Trọng lượng\",\"items\":[{\"name\":\"Kích thước\",\"value\":\" 163.4 x 78 x 8.75 mm\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Trọng lượng sản phẩm\",\"value\":\" 231 g\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Chất liệu\",\"value\":\"IP68\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Màu sắc\",\"value\":\"Xanh Đậm\",\"type\":\"attribute\",\"attr_id\":\"1\"}]},{\"group_name\":\"Bộ xử lý\",\"items\":[{\"name\":\"Phiên bản CPU\",\"value\":\"Apple A19 Pro\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Loại CPU\",\"value\":\" 12-Core\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Số nhân\",\"value\":\" 6\",\"type\":\"text\",\"attr_id\":\"\"}]},{\"group_name\":\"Màn hình\",\"items\":[{\"name\":\"Kích thước màn hình\",\"value\":\"6.9 inch\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Công nghệ  màn hình\",\"value\":\" OLED\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Chuẩn màn hình\",\"value\":\"Super Retina XDR\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Độ phân giải\",\"value\":\" 2868 x 1320 pixel\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Tần số quét\",\"value\":\"165Hz\",\"type\":\"attribute\",\"attr_id\":\"4\"}]},{\"group_name\":\"Lưu trữ\",\"items\":[{\"name\":\"Dung lượng (ROM)\",\"value\":\"256GB\",\"type\":\"attribute\",\"attr_id\":\"2\"}]},{\"group_name\":\"RAM\",\"items\":[{\"name\":\"RAM\",\"value\":\"8GB\",\"type\":\"attribute\",\"attr_id\":\"3\"}]}]', -1, '2025-12-18 06:43:10'),
(107, NULL, 'SP-2B38169E-491', 'Iphone 15 Promax 128GB', 'iphone-15-promax-128gb-6553', 1, 2, 38990000, 40000000, 127, 'uploads/1766040190_thumb_IMG_20250310_145918.jpg', '[{\"group_name\":\"Thông tin hàng hóa\",\"items\":[{\"name\":\"Xuất xứ\",\"value\":\" Trung Quốc\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Thời  điểm ra mắt\",\"value\":\" 09\\/2025\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Thời  gian bảo hành\",\"value\":\" 24 tháng\",\"type\":\"text\",\"attr_id\":\"\"}]},{\"group_name\":\"Thiết kế & Trọng lượng\",\"items\":[{\"name\":\"Kích thước\",\"value\":\" 163.4 x 78 x 8.75 mm\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Trọng lượng sản phẩm\",\"value\":\" 231 g\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Chất liệu\",\"value\":\"IP68\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Màu sắc\",\"value\":\"đỏ vl\",\"type\":\"attribute\",\"attr_id\":\"1\"}]},{\"group_name\":\"Bộ xử lý\",\"items\":[{\"name\":\"Phiên bản CPU\",\"value\":\"Apple A19 Pro\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Loại CPU\",\"value\":\" 12-Core\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Số nhân\",\"value\":\" 6\",\"type\":\"text\",\"attr_id\":\"\"}]},{\"group_name\":\"Màn hình\",\"items\":[{\"name\":\"Kích thước màn hình\",\"value\":\"6.9 inch\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Công nghệ  màn hình\",\"value\":\" OLED\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Chuẩn màn hình\",\"value\":\"Super Retina XDR\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Độ phân giải\",\"value\":\" 2868 x 1320 pixel\",\"type\":\"text\",\"attr_id\":\"\"},{\"name\":\"Tần số quét\",\"value\":\"165Hz\",\"type\":\"attribute\",\"attr_id\":\"4\"}]},{\"group_name\":\"Lưu trữ\",\"items\":[{\"name\":\"Dung lượng (ROM)\",\"value\":\"256GB\",\"type\":\"attribute\",\"attr_id\":\"2\"}]},{\"group_name\":\"RAM\",\"items\":[{\"name\":\"RAM\",\"value\":\"8GB\",\"type\":\"attribute\",\"attr_id\":\"3\"}]}]', -1, '2025-12-18 06:49:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_attribute_values`
--

CREATE TABLE `product_attribute_values` (
  `id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `value_custom` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_attribute_values`
--

INSERT INTO `product_attribute_values` (`id`, `product_id`, `attribute_id`, `option_id`, `value_custom`) VALUES
(319, 107, 2, 14, '256GB'),
(337, 105, 1, 7, 'Xanh Đậm'),
(339, 105, 2, 14, '256GB'),
(361, 105, 4, 19, '165Hz'),
(362, 105, 3, 3, '8GB'),
(370, 107, 4, 19, '165Hz'),
(371, 107, 3, 3, '8GB');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `created_at`) VALUES
(22, 107, 'uploads/1766040637_gal_0_samsung_galaxy_z_fold7_xam_3_0b5e82f071.webp', '2025-12-18 06:50:37'),
(25, 105, 'uploads/products/1766153219_gal_0_samsung_galaxy_z_fold7_xam_1_de1fb8f431.webp', '2025-12-19 14:06:59'),
(26, 105, 'uploads/products/1766153219_gal_1_samsung_galaxy_z_fold7_xam_2_599278fa70.webp', '2025-12-19 14:06:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(0, 'User'),
(1, 'Admin');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role_id` int(11) NOT NULL DEFAULT 0,
  `verification_token` varchar(100) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `google_id` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'default_avt.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `password`, `role_id`, `verification_token`, `reset_token`, `reset_token_expires`, `is_verified`, `created_at`, `google_id`, `phone`, `city`, `district`, `street_address`, `avatar`) VALUES
('83e978d8-4bc5-478e-b2ac-2a3ee7c874f3', 'trần văn', 'A', 'loanthanh3210s1@gmail.com', '$2y$10$knmDRQPTjBunBoSrJnkkSeQRJbVwcYC8XNVLVaRYqQSbrhh3QmiWW', 0, NULL, NULL, NULL, 1, '2025-12-22 07:45:08', NULL, NULL, NULL, NULL, NULL, 'default_avt.png');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `attribute_options`
--
ALTER TABLE `attribute_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attribute_id` (`attribute_id`);

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `category_brand`
--
ALTER TABLE `category_brand`
  ADD PRIMARY KEY (`category_id`,`brand_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders` (`order_id`),
  ADD KEY `fk_products` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Chỉ mục cho bảng `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `attribute_id` (`attribute_id`),
  ADD KEY `option_id` (`option_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_roles` (`role_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `attribute_options`
--
ALTER TABLE `attribute_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT cho bảng `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=372;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `attribute_options`
--
ALTER TABLE `attribute_options`
  ADD CONSTRAINT `attribute_options_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `category_brand`
--
ALTER TABLE `category_brand`
  ADD CONSTRAINT `category_brand_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_brand_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  ADD CONSTRAINT `product_attribute_values_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_attribute_values_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`),
  ADD CONSTRAINT `product_attribute_values_ibfk_3` FOREIGN KEY (`option_id`) REFERENCES `attribute_options` (`id`);

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
