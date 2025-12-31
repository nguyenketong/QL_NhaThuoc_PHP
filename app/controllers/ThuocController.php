<?php
class ThuocController extends Controller
{
    public function danhSach()
    {
        $thuocModel = $this->model('ThuocModel');
        $nhomModel = $this->model('NhomThuocModel');
        
        $filters = [
            'maNhom' => $_GET['nhom'] ?? null,
            'maThuongHieu' => $_GET['thuong_hieu'] ?? null,
            'tuKhoa' => $_GET['search'] ?? null,
            'isActive' => 1
        ];
        
        $data = [
            'title' => 'Danh sách thuốc - ' . STORE_NAME,
            'danhSachThuoc' => $thuocModel->getAll($filters),
            'nhomThuocs' => $nhomModel->getAll(),
            'thuongHieus' => $this->db->query("SELECT * FROM THUONG_HIEU ORDER BY TenThuongHieu")->fetchAll(),
            'filters' => $filters
        ];
        
        $this->view('thuoc/danh-sach', $data);
    }

    public function chiTiet($id = null)
    {
        if (!$id) $this->redirect('thuoc/danhSach');
        
        $thuocModel = $this->model('ThuocModel');
        $thuoc = $thuocModel->getById($id);
        
        if (!$thuoc) $this->redirect('thuoc/danhSach');
        
        $data = [
            'title' => $thuoc['TenThuoc'] . ' - ' . STORE_NAME,
            'thuoc' => $thuoc,
            'thanhPhans' => $thuocModel->getThanhPhan($id),
            'tacDungPhus' => $thuocModel->getTacDungPhu($id),
            'doiTuongs' => $thuocModel->getDoiTuong($id)
        ];
        
        $this->view('thuoc/chi-tiet', $data);
    }

    public function timKiem()
    {
        $tuKhoa = $_GET['tuKhoa'] ?? '';
        if (empty($tuKhoa)) $this->redirect('thuoc/danhSach');
        
        $thuocModel = $this->model('ThuocModel');
        $nhomModel = $this->model('NhomThuocModel');
        
        $data = [
            'title' => 'Tìm kiếm: ' . $tuKhoa,
            'danhSachThuoc' => $thuocModel->getAll(['tuKhoa' => $tuKhoa, 'isActive' => 1]),
            'nhomThuocs' => $nhomModel->getAll(),
            'thuongHieus' => $this->db->query("SELECT * FROM THUONG_HIEU ORDER BY TenThuongHieu")->fetchAll(),
            'tuKhoa' => $tuKhoa
        ];
        
        $this->view('thuoc/danh-sach', $data);
    }

    public function khuyenMai()
    {
        $thuocModel = $this->model('ThuocModel');
        $nhomModel = $this->model('NhomThuocModel');
        
        $data = [
            'title' => 'Sản phẩm khuyến mãi - ' . STORE_NAME,
            'danhSachThuoc' => $thuocModel->getSanPhamKhuyenMai(50),
            'nhomThuocs' => $nhomModel->getAll(),
            'thuongHieus' => $this->db->query("SELECT * FROM THUONG_HIEU ORDER BY TenThuongHieu")->fetchAll(),
            'isKhuyenMai' => true
        ];
        
        $this->view('thuoc/khuyen-mai', $data);
    }

    public function theoNhom($id = null)
    {
        if (!$id) $this->redirect('thuoc/danhSach');
        
        $nhomModel = $this->model('NhomThuocModel');
        $nhom = $nhomModel->getById($id);
        if (!$nhom) $this->redirect('thuoc/danhSach');
        
        $thuocModel = $this->model('ThuocModel');
        
        $data = [
            'title' => $nhom['TenNhomThuoc'] . ' - ' . STORE_NAME,
            'danhSachThuoc' => $thuocModel->getAll(['maNhom' => $id, 'isActive' => 1]),
            'nhomThuocs' => $nhomModel->getAll(),
            'thuongHieus' => $this->db->query("SELECT * FROM THUONG_HIEU ORDER BY TenThuongHieu")->fetchAll(),
            'tenNhom' => $nhom['TenNhomThuoc']
        ];
        
        $this->view('thuoc/danh-sach', $data);
    }
}
