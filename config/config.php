<?php
/**
 * CẤU HÌNH HỆ THỐNG - NHÀ THUỐC THANH HOÀN
 */

// URL cơ sở
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/Ql_NhaThuoc/php');
}

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'ql_nhathuoc');
define('DB_USER', 'root');
define('DB_PASS', '');

// eSMS API
define('ESMS_API_KEY', '1FF178AEFB29AD2C0B61FA1197E244');
define('ESMS_SECRET_KEY', 'C3A8D07128D23B50EBE8D116CC4859');
define('ESMS_BASE_URL', 'http://rest.esms.vn/MainService.svc/json');
define('ESMS_BRAND_NAME', 'NhaThuoc');
define('OTP_MODE', 'dev'); // 'real' hoặc 'dev'

// Thông tin cửa hàng
define('STORE_NAME', 'Nhà Thuốc Tây Thanh Hoàn');
define('STORE_ADDRESS', 'Nguyễn Thiện Thành, Khóm 4, Phường 5, TP Trà Vinh');
define('STORE_PHONE', '0795930020');
define('STORE_EMAIL', 'thanhhoan@gmail.com');

// Thông tin ngân hàng (VietQR API)
define('BANK_ID', '970422'); // MB Bank
define('BANK_ACCOUNT_NO', '0795930020');
define('BANK_ACCOUNT_NAME', 'NGUYEN KE TONG');
define('BANK_TEMPLATE', 'compact2');
