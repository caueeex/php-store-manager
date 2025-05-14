<?php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function formatPrice($price) {
    return 'R$ ' . number_format($price, 2, ',', '.');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function calculateShipping($subtotal) {
    if ($subtotal > 200) {
        return 0;
    } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
        return 15;
    } else {
        return 20;
    }
}
?>