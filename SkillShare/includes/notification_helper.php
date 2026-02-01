<?php
function createNotification($pdo, $user_id, $type, $message, $related_id = null)
{
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, message, related_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $type, $message, $related_id]);
        return true;
    } catch (PDOException $e) {
        // Log error silently
        error_log("Notification Error: " . $e->getMessage());
        return false;
    }
}

function getUnreadNotifications($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function getAllNotifications($pdo, $user_id, $limit = 20)
{
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function markNotificationAsRead($pdo, $notification_id, $user_id)
{
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    return $stmt->execute([$notification_id, $user_id]);
}

function getUnreadCount($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}
?>