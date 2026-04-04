<?php
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function is_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function old($key, $default = '') {
    return $_POST[$key] ?? $default;
}

function page_title($title = '') {
    return $title ? $title . ' | ' . APP_NAME : APP_NAME;
}

function get_count(PDO $pdo, string $table, string $where = '1=1'): int {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM {$table} WHERE {$where}");
    $row = $stmt->fetch();
    return (int)($row['total'] ?? 0);
}

function active_badge($status) {
    return ((int)$status === 1)
        ? '<span class="badge bg-success">Active</span>'
        : '<span class="badge bg-secondary">Inactive</span>';
}
?>
