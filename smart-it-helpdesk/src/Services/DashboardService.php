<?php
namespace App\Services;

use App\Core\Database;

class DashboardService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getStatistics(): array
    {
        $statusCounts = $this->db->query("SELECT status, COUNT(*) as total FROM tickets GROUP BY status")->fetchAll();
        $avgTime = $this->db->query("SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as h FROM tickets WHERE resolved_at IS NOT NULL")->fetch();
        $topTechs = $this->db->query("SELECT u.name, COUNT(t.id) as resolved_count FROM users u JOIN tickets t ON u.id = t.technician_id WHERE t.status IN ('Resolved', 'Closed') GROUP BY u.id")->fetchAll();
        $avgScore = $this->db->query("SELECT AVG(score) as s FROM ratings")->fetch();

        return [
            'status_counts' => $statusCounts,
            'avg_resolution_hours' => round((float)($avgTime['h'] ?? 0), 1),
            'top_technicians' => $topTechs,
            'avg_rating' => round((float)($avgScore['s'] ?? 0), 1)
        ];
    }
}
