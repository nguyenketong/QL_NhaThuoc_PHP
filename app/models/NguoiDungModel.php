<?php
class NguoiDungModel extends Model
{
    protected $table = 'NGUOI_DUNG';

    public function findByPhone($sdt)
    {
        $stmt = $this->db->prepare("SELECT * FROM NGUOI_DUNG WHERE SoDienThoai = ?");
        $stmt->execute([$sdt]);
        return $stmt->fetch();
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM NGUOI_DUNG WHERE Email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function createOrGetByPhone($sdt)
    {
        $user = $this->findByPhone($sdt);
        if (!$user) {
            $this->insert([
                'SoDienThoai' => $sdt,
                'LoaiDangNhap' => 'Phone',
                'VaiTro' => 'User',
                'NgayTao' => date('Y-m-d H:i:s')
            ]);
            return $this->findByPhone($sdt);
        }
        return $user;
    }

    public function saveOtp($maNguoiDung, $otp)
    {
        $expire = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $stmt = $this->db->prepare("UPDATE NGUOI_DUNG SET OTP = ?, OTP_Expire = ? WHERE MaNguoiDung = ?");
        return $stmt->execute([$otp, $expire, $maNguoiDung]);
    }

    public function verifyOtp($sdt, $otp)
    {
        $stmt = $this->db->prepare("SELECT * FROM NGUOI_DUNG WHERE SoDienThoai = ? AND OTP = ? AND OTP_Expire > NOW()");
        $stmt->execute([$sdt, $otp]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Xóa OTP
            $this->db->prepare("UPDATE NGUOI_DUNG SET OTP = NULL, OTP_Expire = NULL WHERE MaNguoiDung = ?")
                     ->execute([$user['MaNguoiDung']]);
        }
        return $user;
    }

    public function updateProfile($maNguoiDung, $data)
    {
        return $this->update($maNguoiDung, $data, 'MaNguoiDung');
    }
}
