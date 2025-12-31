<?php
/**
 * GioHangController - Quản lý giỏ hàng (Cookie-based)
 */
class GioHangController extends Controller
{
    private $cookieKey = 'GioHang';

    // GET: gioHang
    public function index()
    {
        $gioHang = $this->layGioHang();

        // Cập nhật thông tin sản phẩm từ database
        try {
            foreach ($gioHang as &$item) {
                $stmt = $this->db->prepare("SELECT SoLuongTon, HinhAnh, IsActive FROM THUOC WHERE MaThuoc = ?");
                $stmt->execute([$item['MaThuoc']]);
                $thuoc = $stmt->fetch();

                if ($thuoc) {
                    $item['SoLuongTon'] = $thuoc['SoLuongTon'] ?? 0;
                    $item['HinhAnh'] = $thuoc['HinhAnh'];
                    $item['NgungKinhDoanh'] = !$thuoc['IsActive'];
                    $item['KhongKhaDung'] = $item['NgungKinhDoanh'] || $item['SoLuongTon'] <= 0;
                    
                    if ($item['KhongKhaDung']) {
                        $item['DuocChon'] = false;
                    }
                } else {
                    $item['NgungKinhDoanh'] = true;
                    $item['KhongKhaDung'] = true;
                    $item['DuocChon'] = false;
                }
            }
            $this->luuGioHang($gioHang);
        } catch (Exception $e) {
            // Nếu bảng chưa tồn tại, vẫn hiển thị giỏ hàng từ cookie
        }

        $data = [
            'title' => 'Giỏ hàng - ' . STORE_NAME,
            'gioHang' => $gioHang
        ];

        $this->view('gio-hang/index', $data);
    }

    // POST: gioHang/themAjax (AJAX)
    public function themAjax()
    {
        $maThuoc = (int)($_POST['maThuoc'] ?? 0);
        $soLuong = (int)($_POST['soLuong'] ?? 1);

        // Lấy thông tin thuốc
        $stmt = $this->db->prepare("SELECT * FROM THUOC WHERE MaThuoc = ?");
        $stmt->execute([$maThuoc]);
        $thuoc = $stmt->fetch();

        if (!$thuoc) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không tồn tại!']);
        }

        if (!$thuoc['IsActive']) {
            $this->json(['success' => false, 'message' => 'Sản phẩm đã ngừng kinh doanh!']);
        }

        $soLuongTon = $thuoc['SoLuongTon'] ?? 0;
        if ($soLuongTon <= 0) {
            $this->json(['success' => false, 'message' => 'Sản phẩm đã hết hàng!']);
        }

        $gioHang = $this->layGioHang();
        $index = $this->timSanPham($gioHang, $maThuoc);
        $soLuongTrongGio = $index !== false ? $gioHang[$index]['SoLuong'] : 0;

        if ($soLuongTrongGio + $soLuong > $soLuongTon) {
            $this->json(['success' => false, 'message' => "Chỉ còn $soLuongTon sản phẩm trong kho!"]);
        }

        // Tính giá
        $phanTramGiam = $thuoc['PhanTramGiam'] ?? 0;
        $now = date('Y-m-d H:i:s');
        $dangKhuyenMai = $phanTramGiam > 0
            && (empty($thuoc['NgayBatDauKM']) || $thuoc['NgayBatDauKM'] <= $now)
            && (empty($thuoc['NgayKetThucKM']) || $thuoc['NgayKetThucKM'] >= $now);

        $giaGoc = $thuoc['GiaGoc'] ?? $thuoc['GiaBan'] ?? 0;
        $giaBan = $dangKhuyenMai ? ($giaGoc * (100 - $phanTramGiam) / 100) : ($thuoc['GiaBan'] ?? 0);

        if ($index !== false) {
            $gioHang[$index]['SoLuong'] += $soLuong;
            $gioHang[$index]['GiaBan'] = $giaBan;
        } else {
            $gioHang[] = [
                'MaThuoc' => $maThuoc,
                'TenThuoc' => $thuoc['TenThuoc'],
                'HinhAnh' => $thuoc['HinhAnh'] ?? '',
                'GiaBan' => $giaBan,
                'SoLuong' => $soLuong,
                'DuocChon' => true
            ];
        }

        $this->luuGioHang($gioHang);
        $tongSoLuong = array_sum(array_column($gioHang, 'SoLuong'));

        $this->json(['success' => true, 'soLuong' => $tongSoLuong, 'message' => 'Đã thêm vào giỏ hàng!']);
    }

    // POST: gioHang/capNhat
    public function capNhat()
    {
        $maThuoc = (int)($_POST['maThuoc'] ?? 0);
        $soLuong = (int)($_POST['soLuong'] ?? 0);

        $gioHang = $this->layGioHang();
        $index = $this->timSanPham($gioHang, $maThuoc);

        if ($index !== false) {
            if ($soLuong <= 0) {
                array_splice($gioHang, $index, 1);
            } else {
                // Kiểm tra tồn kho
                $stmt = $this->db->prepare("SELECT SoLuongTon FROM THUOC WHERE MaThuoc = ?");
                $stmt->execute([$maThuoc]);
                $soLuongTon = $stmt->fetchColumn() ?? 0;

                if ($soLuong > $soLuongTon) {
                    $this->setFlash('error', "Chỉ còn $soLuongTon sản phẩm trong kho!");
                    $gioHang[$index]['SoLuong'] = $soLuongTon;
                } else {
                    $gioHang[$index]['SoLuong'] = $soLuong;
                }
            }
        }

        $this->luuGioHang($gioHang);
        $this->redirect('gioHang');
    }

    // POST: gioHang/capNhatSoLuong (AJAX)
    public function capNhatSoLuong()
    {
        $maThuoc = (int)($_POST['maThuoc'] ?? 0);
        $soLuong = (int)($_POST['soLuong'] ?? 0);

        $gioHang = $this->layGioHang();
        $index = $this->timSanPham($gioHang, $maThuoc);

        if ($index !== false) {
            if ($soLuong <= 0) {
                array_splice($gioHang, $index, 1);
            } else {
                $stmt = $this->db->prepare("SELECT SoLuongTon FROM THUOC WHERE MaThuoc = ?");
                $stmt->execute([$maThuoc]);
                $soLuongTon = $stmt->fetchColumn() ?? 0;

                if ($soLuong > $soLuongTon) {
                    $this->json(['success' => false, 'message' => "Chỉ còn $soLuongTon sản phẩm trong kho!"]);
                }
                $gioHang[$index]['SoLuong'] = $soLuong;
            }
        }

        $this->luuGioHang($gioHang);
        $tongTien = $this->tinhTongTien($gioHang);
        $this->json(['success' => true, 'tongTien' => $tongTien]);
    }

    // POST: gioHang/capNhatChon (AJAX)
    public function capNhatChon()
    {
        $maThuoc = (int)($_POST['maThuoc'] ?? 0);
        $duocChon = filter_var($_POST['duocChon'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $gioHang = $this->layGioHang();
        $index = $this->timSanPham($gioHang, $maThuoc);

        if ($index !== false && empty($gioHang[$index]['KhongKhaDung'])) {
            $gioHang[$index]['DuocChon'] = $duocChon;
            $this->luuGioHang($gioHang);
        }

        $tongTien = $this->tinhTongTienChon($gioHang);
        $this->json(['success' => true, 'tongTien' => $tongTien]);
    }

    // POST: gioHang/chonTatCa (AJAX)
    public function chonTatCa()
    {
        $chon = filter_var($_POST['chon'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $gioHang = $this->layGioHang();
        foreach ($gioHang as &$item) {
            if (empty($item['KhongKhaDung'])) {
                $item['DuocChon'] = $chon;
            }
        }
        $this->luuGioHang($gioHang);

        $tongTien = $this->tinhTongTienChon($gioHang);
        $this->json(['success' => true, 'tongTien' => $tongTien]);
    }

    // GET: gioHang/xoa/{id}
    public function xoa($maThuoc = null)
    {
        $maThuoc = (int)$maThuoc;
        $gioHang = $this->layGioHang();
        $index = $this->timSanPham($gioHang, $maThuoc);

        if ($index !== false) {
            array_splice($gioHang, $index, 1);
        }

        $this->luuGioHang($gioHang);
        $this->redirect('gioHang');
    }

    // GET: gioHang/xoaTatCa
    public function xoaTatCa()
    {
        $this->xoaGioHangCookie();
        $this->setFlash('success', 'Đã xóa toàn bộ giỏ hàng!');
        $this->redirect('gioHang');
    }

    // GET: gioHang/laySoLuong (AJAX)
    public function laySoLuong()
    {
        $gioHang = $this->layGioHang();
        $soLuong = array_sum(array_column($gioHang, 'SoLuong'));
        $this->json(['soLuong' => $soLuong]);
    }

    // GET: gioHang/thanhToan
    public function thanhToan()
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Vui lòng đăng nhập để thanh toán!');
            $_SESSION['redirect_after_login'] = 'gioHang/thanhToan';
            $this->redirect('user/phoneLogin');
        }

        $gioHang = $this->layGioHang();
        
        // Cập nhật và lọc sản phẩm
        foreach ($gioHang as &$item) {
            $stmt = $this->db->prepare("SELECT SoLuongTon, IsActive FROM THUOC WHERE MaThuoc = ?");
            $stmt->execute([$item['MaThuoc']]);
            $thuoc = $stmt->fetch();

            if ($thuoc) {
                $item['SoLuongTon'] = $thuoc['SoLuongTon'] ?? 0;
                $item['NgungKinhDoanh'] = !$thuoc['IsActive'];
                $item['KhongKhaDung'] = $item['NgungKinhDoanh'] || $item['SoLuongTon'] <= 0;
            } else {
                $item['NgungKinhDoanh'] = true;
                $item['KhongKhaDung'] = true;
            }
        }

        // Lọc sản phẩm được chọn và khả dụng
        $gioHangThanhToan = array_filter($gioHang, function($item) {
            return !empty($item['DuocChon']) && empty($item['KhongKhaDung']);
        });

        if (empty($gioHangThanhToan)) {
            $this->setFlash('error', 'Vui lòng chọn sản phẩm để thanh toán!');
            $this->redirect('gioHang');
        }

        // Lấy thông tin người dùng
        $nguoiDungModel = $this->model('NguoiDungModel');
        $nguoiDung = $nguoiDungModel->getById($this->getUserId());

        $data = [
            'title' => 'Thanh toán - ' . STORE_NAME,
            'gioHang' => array_values($gioHangThanhToan),
            'nguoiDung' => $nguoiDung
        ];

        $this->view('gio-hang/thanh-toan', $data);
    }

    // POST: gioHang/datHang
    public function datHang()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('user/phoneLogin');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('gioHang');
        }

        $diaChiGiaoHang = $_POST['diaChiGiaoHang'] ?? '';
        $phuongThucThanhToan = $_POST['phuongThucThanhToan'] ?? 'Tiền mặt';
        $hinhThucNhanHang = $_POST['hinhThucNhanHang'] ?? 'Giao hàng';

        $gioHang = $this->layGioHang();
        
        // Lọc sản phẩm được chọn
        $gioHangDatHang = array_filter($gioHang, function($item) {
            return !empty($item['DuocChon']) && empty($item['KhongKhaDung']);
        });

        if (empty($gioHangDatHang)) {
            $this->setFlash('error', 'Vui lòng chọn sản phẩm để thanh toán!');
            $this->redirect('gioHang');
        }

        // Xử lý địa chỉ
        if ($hinhThucNhanHang === 'Nhận tại nhà thuốc') {
            $diaChiFinal = 'Nhận tại nhà thuốc: ' . STORE_ADDRESS;
        } elseif (!empty($diaChiGiaoHang)) {
            $diaChiFinal = $diaChiGiaoHang;
        } else {
            $this->setFlash('error', 'Vui lòng nhập địa chỉ giao hàng!');
            $this->redirect('gioHang/thanhToan');
        }

        $tongTien = array_sum(array_map(function($item) {
            return $item['GiaBan'] * $item['SoLuong'];
        }, $gioHangDatHang));

        try {
            $this->db->beginTransaction();

            // Tạo đơn hàng
            $stmt = $this->db->prepare("INSERT INTO DON_HANG (MaNguoiDung, NgayDat, DiaChiGiaoHang, PhuongThucThanhToan, TongTien, TrangThai) VALUES (?, NOW(), ?, ?, ?, 'Chờ xác nhận')");
            $stmt->execute([$this->getUserId(), $diaChiFinal, $phuongThucThanhToan, $tongTien]);
            $maDonHang = $this->db->lastInsertId();

            // Thêm chi tiết đơn hàng
            foreach ($gioHangDatHang as $item) {
                $thanhTien = $item['GiaBan'] * $item['SoLuong'];
                $stmt = $this->db->prepare("INSERT INTO CHI_TIET_DON_HANG (MaDonHang, MaThuoc, SoLuong, DonGia, ThanhTien) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$maDonHang, $item['MaThuoc'], $item['SoLuong'], $item['GiaBan'], $thanhTien]);

                // Cập nhật số lượng tồn
                $stmt = $this->db->prepare("UPDATE THUOC SET SoLuongTon = SoLuongTon - ?, SoLuongDaBan = SoLuongDaBan + ? WHERE MaThuoc = ?");
                $stmt->execute([$item['SoLuong'], $item['SoLuong'], $item['MaThuoc']]);
            }

            $this->db->commit();

            // Xóa sản phẩm đã đặt khỏi giỏ hàng
            $sanPhamConLai = array_filter($gioHang, function($item) {
                return empty($item['DuocChon']) || !empty($item['KhongKhaDung']);
            });

            if (!empty($sanPhamConLai)) {
                $this->luuGioHang(array_values($sanPhamConLai));
            } else {
                $this->xoaGioHangCookie();
            }

            $this->setFlash('success', 'Đặt hàng thành công!');

            if ($phuongThucThanhToan === 'Chuyển khoản') {
                $this->redirect('donHang/thanhToanQR/' . $maDonHang);
            }

            $this->redirect('donHang/chiTiet/' . $maDonHang);

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->setFlash('error', 'Đặt hàng thất bại: ' . $e->getMessage());
            $this->redirect('gioHang/thanhToan');
        }
    }

    // Helper methods
    private function layGioHang()
    {
        if (!isset($_COOKIE[$this->cookieKey])) {
            return [];
        }
        return json_decode($_COOKIE[$this->cookieKey], true) ?: [];
    }

    private function luuGioHang($gioHang)
    {
        setcookie($this->cookieKey, json_encode($gioHang), time() + 30 * 24 * 3600, '/');
    }

    private function xoaGioHangCookie()
    {
        setcookie($this->cookieKey, '', time() - 3600, '/');
    }

    private function timSanPham($gioHang, $maThuoc)
    {
        foreach ($gioHang as $index => $item) {
            if ($item['MaThuoc'] == $maThuoc) {
                return $index;
            }
        }
        return false;
    }

    private function tinhTongTien($gioHang)
    {
        return array_sum(array_map(function($item) {
            return $item['GiaBan'] * $item['SoLuong'];
        }, $gioHang));
    }

    private function tinhTongTienChon($gioHang)
    {
        $filtered = array_filter($gioHang, function($item) {
            return !empty($item['DuocChon']) && empty($item['KhongKhaDung']);
        });
        return $this->tinhTongTien($filtered);
    }
}
