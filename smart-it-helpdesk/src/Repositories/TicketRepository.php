<?php

namespace App\Repositories;

use App\Core\Database;

class TicketRepository implements RepositoryInterface
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT t.*, u.name as user_name, u.email as user_email, 
                       c.name as category_name, tech.name as tech_name, tech.email as tech_email
                FROM tickets t
                JOIN users u ON t.user_id = u.id
                JOIN categories c ON t.category_id = c.id
                LEFT JOIN users tech ON t.technician_id = tech.id
                WHERE t.id = :id LIMIT 1";
        $res = $this->db->query($sql, ['id' => $id])->fetch();
        return $res ?: null;
    }

    public function all(): array
    {
        return $this->db->query(
            "SELECT t.*, u.name as user_name, c.name as category_name 
             FROM tickets t 
             JOIN users u ON t.user_id = u.id 
             JOIN categories c ON t.category_id = c.id 
             ORDER BY t.created_at DESC"
        )->fetchAll();
    }

    public function findByUser(int $userId): array
    {
        return $this->db->query(
            "SELECT t.*, u.name as user_name, c.name as category_name 
             FROM tickets t 
             JOIN users u ON t.user_id = u.id 
             JOIN categories c ON t.category_id = c.id 
             WHERE t.user_id = :uid 
             ORDER BY t.created_at DESC", 
            ['uid' => $userId]
        )->fetchAll();
    }

    public function findByTechnician(int $techId): array
    {
        return $this->db->query(
            "SELECT t.*, u.name as user_name, c.name as category_name 
             FROM tickets t 
             JOIN users u ON t.user_id = u.id 
             JOIN categories c ON t.category_id = c.id 
             WHERE t.technician_id = :tid 
             ORDER BY t.created_at DESC", 
            ['tid' => $techId]
        )->fetchAll();
    }

    public function create(array $data): int
    {
        $this->db->query(
            "INSERT INTO tickets (user_id, category_id, title, description, priority, image_path, status) 
             VALUES (:u, :c, :t, :d, :p, :img, 'Open')",
            [
                'u' => $data['user_id'],
                'c' => $data['category_id'],
                't' => $data['title'],
                'd' => $data['description'],
                'p' => $data['priority'],
                'img' => $data['image_path'] ?? null
            ]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];
        foreach ($data as $k => $v) {
            $fields[] = "{$k} = :{$k}";
            $params[$k] = $v;
        }
        return $this->db->query("UPDATE tickets SET " . implode(', ', $fields) . " WHERE id = :id", $params)->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->query("DELETE FROM tickets WHERE id = :id", ['id' => $id])->rowCount() > 0;
    }

    public function getCategories(): array
    {
        return $this->db->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
    }

    public function getTechnicians(): array
    {
        return $this->db->query("SELECT id, name, email FROM users WHERE role = 'technician'")->fetchAll();
    }

    public function getComments(int $ticketId): array
    {
        return $this->db->query(
            "SELECT c.*, u.name as author_name, u.role as author_role 
             FROM comments c JOIN users u ON c.user_id = u.id 
             WHERE c.ticket_id = :tid ORDER BY c.created_at ASC",
            ['tid' => $ticketId]
        )->fetchAll();
    }

    public function addComment(int $ticketId, int $userId, string $body, ?string $image = null): void
    {
        $this->db->query(
            "INSERT INTO comments (ticket_id, user_id, body, image_path) VALUES (:t, :u, :b, :img)",
            ['t' => $ticketId, 'u' => $userId, 'b' => $body, 'img' => $image]
        );
    }

    public function logStatus(int $ticketId, int $userId, string $from, string $to, ?string $note = null): void
    {
        $this->db->query(
            "INSERT INTO status_logs (ticket_id, changed_by, from_status, to_status, note) VALUES (:t, :u, :f, :to, :n)",
            ['t' => $ticketId, 'u' => $userId, 'f' => $from, 'to' => $to, 'n' => $note]
        );
    }

    public function getLogs(int $ticketId): array
    {
        return $this->db->query(
            "SELECT l.*, u.name as changer_name FROM status_logs l JOIN users u ON l.changed_by = u.id WHERE l.ticket_id = :t ORDER BY l.created_at ASC",
            ['t' => $ticketId]
        )->fetchAll();
    }

    public function getRating(int $ticketId): ?array
    {
        $res = $this->db->query("SELECT * FROM ratings WHERE ticket_id = :t LIMIT 1", ['t' => $ticketId])->fetch();
        return $res ?: null;
    }

    public function addRating(int $ticketId, int $score, ?string $feedback = null): void
    {
        $sql = "INSERT INTO ratings (ticket_id, score, feedback) 
                VALUES (:t, :s, :f)
                ON DUPLICATE KEY UPDATE 
                    score = VALUES(score), 
                    feedback = VALUES(feedback), 
                    created_at = CURRENT_TIMESTAMP";

        $this->db->query($sql, [
            't' => $ticketId,
            's' => $score,
            'f' => $feedback
        ]);
    }
}