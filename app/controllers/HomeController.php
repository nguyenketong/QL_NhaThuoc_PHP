<?php
class HomeController extends Controller
{
    public function index()
    {
        $thuocModel = $this->model('ThuocModel');
        $nhomModel = $this->model('NhomThuocModel');
        
        $data = [
            'title' => 'Trang chủ - ' . STORE_NAME,
            'sanPhamMoi' => $thuocModel->getSanPhamMoi(10),
            'sanPhamKhuyenMai' => $thuocModel->getSanPhamKhuyenMai(10),
            'sanPhamBanChay' => $thuocModel->getSanPhamBanChay(10),
            'nhomThuocs' => $nhomModel->getAll(),
            'thuongHieus' => $this->db->query("SELECT * FROM THUONG_HIEU ORDER BY TenThuongHieu")->fetchAll(),
            'baiViets' => $this->db->query("SELECT * FROM BAIVIET WHERE IsActive = 1 ORDER BY NgayDang DESC LIMIT 10")->fetchAll()
        ];
        
        $this->view('home/index', $data);
    }

    public function gioiThieu()
    {
        $this->view('home/gioi-thieu', ['title' => 'Giới thiệu - ' . STORE_NAME]);
    }

    public function lienHe()
    {
        $this->view('home/lien-he', ['title' => 'Liên hệ - ' . STORE_NAME]);
    }
}
