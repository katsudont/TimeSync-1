<?php

namespace App\Controllers;

use App\Models\User;

class ProfileController extends BaseController
{
    public function index()
    {
        
        session_start();

        
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            header('Location: /login');
            exit;
        }

        
        $userModel = new User();

        
        $profileData = $userModel->getProfileData($_SESSION['user_id']);

        
        $data = [
            'username' => $_SESSION['username'] ?? 'User', 
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

        error_log("Updating profile for user ID: $userId with Name: $name, Email: $email"); 

        $userModel = new User();
        $result = $userModel->updateProfile($userId, $name, $email, $username, $birthdate);

        if ($result) {
            error_log("Profile updated successfully."); 
        } else {
            error_log("Failed to update profile."); 
        }

        header('Location: /profile');
        exit;
    }
    }
}
