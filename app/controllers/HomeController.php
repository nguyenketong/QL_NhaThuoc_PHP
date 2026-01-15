<?php
class HomeController extends Controller
{
    public function index()
    {
        $thuocModel = $this->model('ThuocModel');
        $nhomModel = $this->model('NhomThuocModel');
        
        // Lấy bài viết: ưu tiên bài nổi bật trước
        $baiViets = $this->db->query("
            SELECT * FROM BAIVIET 
            WHERE IsActive = 1 
            ORDER BY IsNoiBat DESC, NgayDang DESC 
            LIMIT 5
        ")->fetchAll();
        
        $data = [
            'title' => 'Trang chủ - ' . STORE_NAME,
            'sanPhamMoi' => $thuocModel->getSanPhamMoi(10),
            'sanPhamKhuyenMai' => $thuocModel->getSanPhamKhuyenMai(10),
            'sanPhamBanChay' => $thuocModel->getSanPhamBanChay(10),
            'nhomThuocs' => $nhomModel->getAll(),
            'thuongHieus' => $this->db->query("SELECT * FROM THUONG_HIEU ORDER BY TenThuongHieu")->fetchAll(),
            'baiViets' => $baiViets
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
