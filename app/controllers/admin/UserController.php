<?php
session_start();
require_once __DIR__ . '/../../models/Admin/User.php';
require_once __DIR__ . '/../../models/Admin/Log.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../../../../vendor/PHPMailer/src/Exception.php";
require "../../../../vendor/PHPMailer/src/PHPMailer.php";
require "../../../../vendor/PHPMailer/src/SMTP.php";

class UserController
{

    private $user;
    private $log;

    public function __construct()
    {
        $this->user = new User();
        $this->log  = new Log();
    }

    public function index($params)
    {
        return $this->user->getAll($params);
    }

    public function store($data)
    {
        if ($_SESSION['user_role'] !== 'ADMIN') return false;

        $result = $this->user->create($data);
        $admin_id = $_SESSION['user_id'];
        // $admin_id = 5;
        if ($result) {
            $newUser = $this->user->findByEmail($data['email']);

            $this->log->write(
                $admin_id,
                'CREATE_USER',
                $newUser['id'],
                'USER',
                $newUser['role'],
                "Tạo tài khoản {$newUser['name']}"
            );
        }

        return $result;
    }

    public function update($id, $data)
    {
        if ($_SESSION['user_role'] !== 'ADMIN') return false;
        $admin_id = $_SESSION['user_id'];
        // $admin_id = 5;
        $targetUser = $this->user->findById($id);

        if (!$targetUser) return false;

        $result = $this->user->update($id, $data);

        if ($result) {
            $this->log->write(
                $admin_id,
                'UPDATE_USER',
                $id,
                'USER',
                $targetUser['role'],
                "Cập nhật tài khoản {$targetUser['name']}"
            );
        }

        return $result;
    }

    public function lock($id)
    {
        if ($_SESSION['user_role'] !== 'ADMIN') return false;

        $targetUser = $this->user->findById($id);
        $admin_id = $_SESSION['user_id'];
        // $admin_id = 5;
        if (!$targetUser) return false;

        $result = $this->user->changeStatus($id, 'BLOCKED');

        if ($result) {
            $this->log->write(
                $admin_id,
                'LOCK_ACCOUNT',
                $id,
                'USER',
                $targetUser['role'],
                "Khóa tài khoản {$targetUser['name']}"
            );
        }

        return $result;
    }

    public function unlock($id)
    {
        if ($_SESSION['user_role'] !== 'ADMIN') return false;

        $targetUser = $this->user->findById($id);
        $admin_id = $_SESSION['user_id'];
        // $admin_id = 5;
        if (!$targetUser) return false;

        $result = $this->user->changeStatus($id, 'ACTIVE');

        if ($result) {
            $this->log->write(
                $admin_id,
                'UNLOCK_ACCOUNT',
                $id,
                'USER',
                $targetUser['role'],
                "Mở khóa tài khoản {$targetUser['name']}"
            );
        }

        return $result;
    }

    public function reset($id)
    {
        return $this->user->resetPassword($id);
            if ($_SESSION['user_role'] !== 'ADMIN') {
            return false;
        }

        $tempPassword = $this->user->resetPassword($id);
        $targetUser = $this->user->findById($id);
        $admin_id = $_SESSION['user_id'];
        // $admin_id = 5;
        if ($tempPassword) {

            $this->log->write(
                $admin_id,      // actor
                'RESET_PASSWORD',          // action
                $id,                       // target_id
                'USER',                    // target_type
                $targetUser['role'],                      // target_role
                "Admin reset mật khẩu cho user ID $id"
            );

            return $tempPassword;
        }

        return false;
    }
    public function show($id)
    {
        return $this->user->findById($id);
    }
    public function sendMail($id)
    {

        $user = $this->user->sendResetMail($id);

        if (!$user) {
            return false;
            exit;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tungluong230705@gmail.com';
            $mail->Password   = 'kgkw rmji wwyv nfwd';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->setFrom('tungluong230705@gmail.com', 'Hệ thống quản lý');
            $mail->addAddress($user['email'], $user['name']);

            $mail->isHTML(true);
            $mail->Subject = 'Reset mật khẩu';

            $mail->Body = "
                Xin chào <b>{$user['name']}</b><br><br>
                Mật khẩu mới của bạn là: 
                <b>{$user['temp_password']}</b><br><br>
                ⚠ Vui lòng đổi mật khẩu ngay sau khi đăng nhập.
            ";
            if ($mail->send()) {

                $this->user->clearReset($id);
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
