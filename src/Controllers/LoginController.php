<?php

namespace App\Controllers;

use App\Models\User;

class LoginController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        // Initialize the user model
        $this->userModel = new User();
    }

    public function login()
    {
        session_start();

        // Check if the user is locked out due to too many failed attempts
        if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 3) {
            // Check if the last failed attempt was less than 15 seconds ago
            if (isset($_SESSION['last_failed_attempt']) && (time() - $_SESSION['last_failed_attempt']) < 15) {
                $data = [
                    'title' => 'Login',
                    'error' => 'Too many failed attempts. Please try again in 15 seconds.',
                ];
                return $this->render('login', $data);
            } else {
                // Reset failed attempts after the 15-second timeout
                $_SESSION['login_attempts'] = 0;
                unset($_SESSION['last_failed_attempt']);
            }
        }

        // Process the form if POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Input validation
            if (empty($username) || empty($password)) {
                $data = [
                    'title' => 'Login',
                    'error' => 'Please enter both username and password.',
                ];
                return $this->render('login', $data);
            }

            // Authenticate user by passing both username and password
            $user = $this->userModel->login($username, $password); // Pass both arguments

            // Check if user exists and verify password
            if ($user) {
                // Reset login attempts on successful login
                $_SESSION['login_attempts'] = 0;
                $_SESSION['last_failed_attempt'] = null;

                // Store session data
                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];

                // Redirect based on user role
                $this->redirectToDashboard();
            } else {
                // Increment login attempts and show error
                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
                $_SESSION['last_failed_attempt'] = time(); // Log time of failed attempt

                $data = [
                    'title' => 'Login',
                    'error' => 'Invalid credentials. Please try again.',
                ];
                return $this->render('login', $data);
            }
        }

        // Display login page
        $data = ['title' => 'Login'];
        return $this->render('login', $data);
    }

    // Helper function to redirect based on user role
    private function redirectToDashboard()
    {
        // Redirect to appropriate dashboard based on role
        if ($_SESSION['role_id'] == 1) {
            header('Location: /admin-dashboard');
        } elseif ($_SESSION['role_id'] == 2) {
            header('Location: /employee-dashboard');
        } else {
            // Default redirect if no matching role found
            header('Location: /dashboard');
        }
        exit();
    }
}
