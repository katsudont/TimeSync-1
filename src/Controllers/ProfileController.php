<?php

namespace App\Controllers;

use App\Models\User;

class ProfileController extends BaseController
{
    public function index()
    {
        // Start session (only once)
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            header('Location: /login');
            exit;
        }

        // Initialize the User model
        $userModel = new User();

        // Fetch user profile data
        $profileData = $userModel->getProfileData($_SESSION['user_id']);

        // Prepare data for the view
        $data = [
            'username' => $_SESSION['username'] ?? 'User', // Default username
            'profile' => $profileData
        ];

        return $this->render('profile', $data);
    }

    public function update()
    {
    session_start();

    if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
        header('Location: /login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_SESSION['user_id'];
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $username = $_POST['username'] ?? '';
        $birthdate = $_POST['birthdate'] ?? '';

        error_log("Updating profile for user ID: $userId with Name: $name, Email: $email"); // Debug log

        $userModel = new User();
        $result = $userModel->updateProfile($userId, $name, $email, $username, $birthdate);

        if ($result) {
            error_log("Profile updated successfully."); // Debug success
        } else {
            error_log("Failed to update profile."); // Debug failure
        }

        header('Location: /profile');
        exit;
    }
    }
}
