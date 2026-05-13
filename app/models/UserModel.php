<?php

namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM super_admins WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM super_admins WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function updateLastLogin($id)
    {
        $stmt = $this->db->prepare("UPDATE super_admins SET last_login_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function updatePassword($email, $hashedPassword)
    {
        $stmt = $this->db->prepare("UPDATE super_admins SET password_hash = :password WHERE email = :email");
        return $stmt->execute([
            'password' => $hashedPassword,
            'email' => $email
        ]);
    }
}
