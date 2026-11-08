<?php

class User extends Model
{
    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id_user = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function changePassword($id, $currentPassword, $newPassword)
    {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id_user = ? LIMIT 1");
        $stmt->execute([(int) $id]);
        $storedPassword = $stmt->fetchColumn();
        if (!$storedPassword || !password_verify($currentPassword, $storedPassword)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id_user = ?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), (int) $id]);
    }

    public function isCurrentlyActive($user)
    {
        $today = date('Y-m-d');
        return $user
            && (int) $user['is_active'] === 1
            && $user['aktif_mulai'] <= $today
            && $user['aktif_sampai'] >= $today;
    }

    public function countByRole($role)
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM users
             WHERE role = ? AND is_active = 1
             AND aktif_mulai <= ? AND aktif_sampai >= ?"
        );
        $stmt->execute([$role, $today, $today]);
        return (int) $stmt->fetchColumn();
    }

    public function countAll()
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function allByStatus($active)
    {
        $today = date('Y-m-d');
        $condition = $active
            ? 'is_active = 1 AND aktif_mulai <= ? AND aktif_sampai >= ?'
            : '(is_active = 0 OR aktif_mulai > ? OR aktif_sampai < ?)';
        $stmt = $this->db->prepare(
            "SELECT id_user, nama, username, role, is_active, aktif_mulai, aktif_sampai
             FROM users WHERE $condition ORDER BY nama"
        );
        $stmt->execute([$today, $today]);
        return $stmt->fetchAll();
    }

    public function create($nama, $username, $password, $role, $activeFrom = null, $activeUntil = null)
    {
        $activeFrom = $activeFrom ?: date('Y-m-d');
        $activeUntil = $activeUntil ?: '2099-12-31';
        $stmt = $this->db->prepare(
            "INSERT INTO users (nama, username, password, role, aktif_mulai, aktif_sampai)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $nama,
            $username,
            password_hash($password, PASSWORD_DEFAULT),
            $role,
            $activeFrom,
            $activeUntil
        ]);
    }

    public function update($id, $nama, $username, $role, $activeFrom, $activeUntil, $password = '')
    {
        $params = [$nama, $username, $role, $activeFrom, $activeUntil];
        $passwordSql = '';
        if ($password !== '') {
            $passwordSql = ', password = ?';
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }
        $params[] = $id;

        $stmt = $this->db->prepare(
            "UPDATE users SET nama = ?, username = ?, role = ?, aktif_mulai = ?, aktif_sampai = ?
             $passwordSql WHERE id_user = ?"
        );
        return $stmt->execute($params);
    }

    public function setActive($id, $active)
    {
        if ($active) {
            $stmt = $this->db->prepare(
                "UPDATE users SET is_active = 1
                 WHERE id_user = ? AND aktif_mulai <= ? AND aktif_sampai >= ?"
            );
            $today = date('Y-m-d');
            return $stmt->execute([$id, $today, $today]) && $stmt->rowCount() > 0;
        } else {
            $stmt = $this->db->prepare("UPDATE users SET is_active = 0 WHERE id_user = ?");
        }
        return $stmt->execute([$id]) && $stmt->rowCount() > 0;
    }

    public function deleteIfInactive($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id_user = ? AND is_active = 0");
        return $stmt->execute([$id]) && $stmt->rowCount() > 0;
    }

    public function countCurrentActiveAdmins()
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM users
             WHERE role = 'admin' AND is_active = 1
             AND aktif_mulai <= ? AND aktif_sampai >= ?"
        );
        $stmt->execute([$today, $today]);
        return (int) $stmt->fetchColumn();
    }

    public function doctorsActive()
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare(
            "SELECT id_user, nama, username, role, is_active
             FROM users
             WHERE role = 'dokter' AND is_active = 1
             AND aktif_mulai <= ? AND aktif_sampai >= ?
             ORDER BY nama"
        );
        $stmt->execute([$today, $today]);
        return $stmt->fetchAll();
    }

    public function auditTerbaru($limit = 10)
    {
        $limit = (int) $limit;
        $sql = "SELECT a.*, u.nama
                FROM audit_trail a
                LEFT JOIN users u ON u.id_user = a.id_user
                ORDER BY a.id_audit DESC
                LIMIT $limit";
        return $this->db->query($sql)->fetchAll();
    }
}
