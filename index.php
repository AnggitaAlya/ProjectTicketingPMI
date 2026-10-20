<?php
session_start();
if (isset($_SESSION['role'])) {
    header("Location: dashboard/dashboard.php");
    exit();
} else {
    header("Location: auth/login.php");
    exit();
}
include './config/session_check.php';
