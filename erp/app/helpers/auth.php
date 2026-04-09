<?php

function auth_user() {
    return $_SESSION['user'] ?? null;
}

function require_auth() {
    if (!auth_user()) {
        header('Location: index.php?action=login');
        exit;
    }
}

function is_admin() {
    return auth_user() && auth_user()['role'] === 'admin';
}