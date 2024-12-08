<?php

namespace App\Controllers;

use App\Models\User;

class LoginController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        
        $this->userModel = new User();
    }

    public function login()
    {
        session_start();

        
        if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 3) {
            
            if (isset($_SESSION['last_failed_attempt']) && (time() - $_SESSION['last_failed_attempt']) < 15) {
                $data = [
                    'title' => 'Login',
                    'error' => 'Too many failed attempts. Please try again in 15 seconds.',
                ];
                return $this->render('login', $data);
            } else {
               
                $_SESSION['login_attempts'] = 0;
                unset($_SESSION['last_failed_attempt']);
            }
        }

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

           
            if (empty($username) || empty($password)) {
                $data = [
                    'title' => 'Login',
                    'error' => 'Please enter both username and password.',
                ];
                return $this->render('login', $data);
            }

            
            $user = $this->userModel->login($username, $password); 

            
            if ($user) {
                
                $_SESSION['login_attempts'] = 0;
                $_SESSION['last_failed_attempt'] = null;

                
                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];

                
                $this->redirectToDashboard();
            } else {
                
                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
                $_SESSION['last_failed_attempt'] = time(); 

                $data = [
                    'title' => 'Login',
                    'error' => 'Invalid credentials. Please try again.',
                ];
                return $this->render('login', $data);
            }
        }

        
        $data = ['title' => 'Login'];
        return $this->render('login', $data);
    }

    
    private function redirectToDashboard()
    {
        
        if ($_SESSION['role_id'] == 1) {
            header('Location: /admin-dashboard');
        } elseif ($_SESSION['role_id'] == 2) {
            header('Location: /employee-dashboard');
        } else {
            
            header('Location: /dashboard');
        }
        exit();
    }
}
