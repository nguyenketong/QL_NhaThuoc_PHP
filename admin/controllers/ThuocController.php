<?php
/**
 * Thuoc Controller - Quản lý thuốc
 */
class ThuocController extends AdminController
{
    public function index()
    {
        $search = $_GET['search'] ?? '';
        
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu, nsx.TenNuocSX
                FROM THUOC t
                LEFT JOIN NHOM_THUOC nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN THUONG_HIEU th ON t.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN NUOC_SAN_XUAT nsx ON t.MaNuocSX = nsx.MaNuocSX";
        
        if ($search) {
            $sql .= " WHERE t.TenThuoc LIKE :search";
        }
        $sql .= " ORDER BY t.MaThuoc DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($search) {
            $stmt->execute(['search' => "%$search%"]);
        } else {
            $stmt->execute();
        }
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('thuoc/index', [
            'title' => 'Quản lý thuốc',
            'danhSach' => $danhSach,
            'search' => $search
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $data = [
                'TenThuoc' => $_POST['TenThuoc'] ?? '',
                'MoTa' => $_POST['MoTa'] ?? '',
                'DonViTinh' => $_POST['DonViTinh'] ?? '',
                'GiaBan' => $_POST['GiaBan'] ?? 0,
                'GiaGoc' => $_POST['GiaGoc'] ?: null,
                'PhanTramGiam' => $_POST['PhanTramGiam'] ?: null,
                'SoLuongTon' => $_POST['SoLuongTon'] ?? 0,
                'MaNhomThuoc' => $_POST['MaNhomThuoc'] ?: null,
                'MaThuongHieu' => $_POST['MaThuongHieu'] ?: null,
                'MaNuocSX' => $_POST['MaNuocSX'] ?: null,
                'NgayBatDauKM' => $_POST['NgayBatDauKM'] ?: null,
                'NgayKetThucKM' => $_POST['NgayKetThucKM'] ?: null,
                'IsHot' => isset($_POST['IsHot']) ? 1 : 0,
                'IsNew' => isset($_POST['IsNew']) ? 1 : 0,
                'IsActive' => isset($_POST['IsActive']) ? 1 : 0,
                'NgayTao' => date('Y-m-d H:i:s')
            ];

            // Upload hình ảnh
            if (isset($_FILES['hinhAnhFile']) && $_FILES['hinhAnhFile']['size'] > 0) {
                $data['HinhAnh'] = $this->uploadImage($_FILES['hinhAnhFile'], 'images');
            } elseif (!empty($_POST['HinhAnh'])) {
                $data['HinhAnh'] = $_POST['HinhAnh'];
            }

            // Insert thuốc
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            $stmt = $this->db->prepare("INSERT INTO THUOC ($columns) VALUES ($placeholders)");
            $stmt->execute($data);
            $maThuoc = $this->db->lastInsertId();

            // Lưu thành phần
            $thanhPhanIds = $_POST['ThanhPhanIds'] ?? [];
            $hamLuongs = $_POST['HamLuongs'] ?? [];
            foreach ($thanhPhanIds as $i => $tpId) {
                if ($tpId > 0) {
                    $stmt = $this->db->prepare("INSERT INTO CT_THANH_PHAN (MaThuoc, MaThanhPhan, HamLuong) VALUES (?, ?, ?)");
                    $stmt->execute([$maThuoc, $tpId, $hamLuongs[$i] ?? null]);
                }
            }

            // Lưu tác dụng phụ
            $tacDungPhuIds = $_POST['TacDungPhuIds'] ?? [];
            $mucDos = $_POST['MucDos'] ?? [];
            foreach ($tacDungPhuIds as $i => $tdpId) {
                if ($tdpId > 0) {
                    $stmt = $this->db->prepare("INSERT INTO CT_TAC_DUNG_PHU (MaThuoc, MaTacDungPhu, MucDo) VALUES (?, ?, ?)");
                    $stmt->execute([$maThuoc, $tdpId, $mucDos[$i] ?? null]);
                }
            }

            // Lưu đối tượng sử dụng
            $doiTuongIds = $_POST['DoiTuongIds'] ?? [];
            foreach ($doiTuongIds as $dtId) {
                $stmt = $this->db->prepare("INSERT INTO CT_DOI_TUONG (MaThuoc, MaDoiTuong) VALUES (?, ?)");
                $stmt->execute([$maThuoc, $dtId]);
            }

            $this->setFlash('success', 'Thêm thuốc thành công!');
            $this->redirect('?controller=thuoc');
            return;
        }

        $this->loadDropdowns();
        $this->view('thuoc/create', ['title' => 'Thêm thuốc mới']);
    }

    public function edit($id)
    {
        // Lấy thông tin thuốc
        $stmt = $this->db->prepare("SELECT * FROM THUOC WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        $thuoc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$thuoc) {
            $this->redirect('?controller=thuoc');
            return;
        }

        if ($this->isPost()) {
            $data = [
                'TenThuoc' => $_POST['TenThuoc'] ?? '',
                'MoTa' => $_POST['MoTa'] ?? '',
                'DonViTinh' => $_POST['DonViTinh'] ?? '',
                'GiaBan' => $_POST['GiaBan'] ?? 0,
                'GiaGoc' => $_POST['GiaGoc'] ?: null,
                'PhanTramGiam' => $_POST['PhanTramGiam'] ?: null,
                'SoLuongTon' => $_POST['SoLuongTon'] ?? 0,
                'MaNhomThuoc' => $_POST['MaNhomThuoc'] ?: null,
                'MaThuongHieu' => $_POST['MaThuongHieu'] ?: null,
                'MaNuocSX' => $_POST['MaNuocSX'] ?: null,
                'NgayBatDauKM' => $_POST['NgayBatDauKM'] ?: null,
                'NgayKetThucKM' => $_POST['NgayKetThucKM'] ?: null,
                'IsHot' => isset($_POST['IsHot']) ? 1 : 0,
                'IsNew' => isset($_POST['IsNew']) ? 1 : 0,
                'IsActive' => isset($_POST['IsActive']) ? 1 : 0
            ];

            // Upload hình ảnh mới
            if (isset($_FILES['hinhAnhFile']) && $_FILES['hinhAnhFile']['size'] > 0) {
                $data['HinhAnh'] = $this->uploadImage($_FILES['hinhAnhFile'], 'images');
            } elseif (!empty($_POST['HinhAnh'])) {
                $data['HinhAnh'] = $_POST['HinhAnh'];
            } else {
                $data['HinhAnh'] = $thuoc['HinhAnh'];
            }

            // Update thuốc
            $sets = [];
            foreach ($data as $key => $value) {
                $sets[] = "$key = :$key";
            }
            $data['id'] = $id;
            $stmt = $this->db->prepare("UPDATE THUOC SET " . implode(', ', $sets) . " WHERE MaThuoc = :id");
            $stmt->execute($data);

            // Xóa dữ liệu cũ
            $this->db->prepare("DELETE FROM CT_THANH_PHAN WHERE MaThuoc = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM CT_TAC_DUNG_PHU WHERE MaThuoc = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM CT_DOI_TUONG WHERE MaThuoc = ?")->execute([$id]);

            // Lưu thành phần mới
            $thanhPhanIds = $_POST['ThanhPhanIds'] ?? [];
            $hamLuongs = $_POST['HamLuongs'] ?? [];
            foreach ($thanhPhanIds as $i => $tpId) {
                if ($tpId > 0) {
                    $stmt = $this->db->prepare("INSERT INTO CT_THANH_PHAN (MaThuoc, MaThanhPhan, HamLuong) VALUES (?, ?, ?)");
                    $stmt->execute([$id, $tpId, $hamLuongs[$i] ?? null]);
                }
            }

            // Lưu tác dụng phụ mới
            $tacDungPhuIds = $_POST['TacDungPhuIds'] ?? [];
            $mucDos = $_POST['MucDos'] ?? [];
            foreach ($tacDungPhuIds as $i => $tdpId) {
                if ($tdpId > 0) {
                    $stmt = $this->db->prepare("INSERT INTO CT_TAC_DUNG_PHU (MaThuoc, MaTacDungPhu, MucDo) VALUES (?, ?, ?)");
                    $stmt->execute([$id, $tdpId, $mucDos[$i] ?? null]);
                }
            }

            // Lưu đối tượng sử dụng mới
            $doiTuongIds = $_POST['DoiTuongIds'] ?? [];
            foreach ($doiTuongIds as $dtId) {
                $stmt = $this->db->prepare("INSERT INTO CT_DOI_TUONG (MaThuoc, MaDoiTuong) VALUES (?, ?)");
                $stmt->execute([$id, $dtId]);
            }

            $this->setFlash('success', 'Cập nhật thuốc thành công!');
            $this->redirect('?controller=thuoc');
            return;
        }

        // Lấy thành phần, tác dụng phụ, đối tượng đã chọn
        $stmt = $this->db->prepare("SELECT * FROM CT_THANH_PHAN WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        $selectedThanhPhans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT * FROM CT_TAC_DUNG_PHU WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        $selectedTacDungPhus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT MaDoiTuong FROM CT_DOI_TUONG WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        $selectedDoiTuongs = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $this->loadDropdowns();
        $this->view('thuoc/edit', [
            'title' => 'Sửa thuốc',
            'thuoc' => $thuoc,
            'selectedThanhPhans' => $selectedThanhPhans,
            'selectedTacDungPhus' => $selectedTacDungPhus,
            'selectedDoiTuongs' => $selectedDoiTuongs
        ]);
    }

    public function delete($id)
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("DELETE FROM THUOC WHERE MaThuoc = ?");
            $stmt->execute([$id]);
            $this->setFlash('success', 'Xóa thuốc thành công!');
        }
        $this->redirect('?controller=thuoc');
    }

    private function loadDropdowns()
    {
        // Nhóm thuốc
        $stmt = $this->db->query("SELECT * FROM NHOM_THUOC ORDER BY TenNhomThuoc");
        $this->data['nhomThuocs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Thương hiệu
        $stmt = $this->db->query("SELECT * FROM THUONG_HIEU ORDER BY TenThuongHieu");
        $this->data['thuongHieus'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Nước sản xuất
        $stmt = $this->db->query("SELECT * FROM NUOC_SAN_XUAT ORDER BY TenNuocSX");
        $this->data['nuocSXs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Thành phần
        $stmt = $this->db->query("SELECT * FROM THANH_PHAN ORDER BY TenThanhPhan");
        $this->data['thanhPhans'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tác dụng phụ
        $stmt = $this->db->query("SELECT * FROM TAC_DUNG_PHU ORDER BY TenTacDungPhu");
        $this->data['tacDungPhus'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đối tượng sử dụng
        $stmt = $this->db->query("SELECT * FROM DOI_TUONG_SU_DUNG ORDER BY TenDoiTuong");
        $this->data['doiTuongs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
