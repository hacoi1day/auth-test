<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\Hash;

class Auth extends BaseController
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function getLogin()
    {
        return view('auth/login');
    }

    public function postLogin()
    {
        $validation = $this->validate([
            'email' => [
                'rules' => 'required|valid_email|is_not_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Enter a valid email address',
                    'is_not_unique' => 'This email is not registered on our service'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[5]|max_length[12]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Password must have at least 5 characters in length',
                    'max_length' => 'Password mush not have more than 12 characters in length'
                ]
            ]
        ]);

        if (!$validation) {
            return view('auth/login', [
                'validation' => $this->validator
            ]);
        } else {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $userModel = new UserModel();
            $user = $userModel->where('email', $email)->first();
            $check_password = Hash::check($password, $user['password']);

            if (intval($user['status']) === 1) {
                session()->setFlashdata('fail', 'Account locked');
                return redirect()->to('/auth/login')->withInput();
            }

            if (!$check_password) {
                $userModel->update($user['id'], [
                    'count_login_failed' => intval($user['count_login_failed']) + 1,
                ]);
                if ($user['count_login_failed'] === 5) {
                    $userModel->update($user['id'], [
                        'status' => 1, // Lock account
                    ]);
                }
                session()->setFlashdata('fail', 'Incorrect password');
                return redirect()->to('/auth/login')->withInput();
            } else {
                $user_id = $user;
                $userModel->update($user['id'], [
                    'count_login_failed' => 0,
                    'last_login' => date('Y-m-d h:i:s')
                ]);
                session()->set('loggedUser', $user_id);
                return redirect()->to('/dashboard');
            }
        }

    }

    public function getRegister()
    {
        return view('auth/register');
    }

    public function postRegister()
    {
        // $validation = $this->validate([
        //     'name' => 'required',
        //     'email' => 'required|valid_email|is_unique[users.email]',
        //     'password' => 'required|min_length[5]|max_length[12]',
        //     'confirm_password' => 'required|min_length[5]|max_length[12]|matches[password]'
        // ]);

        $validation = $this->validate([
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Your full name is required'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'You mush enter a valid email',
                    'is_unique' => 'Email already taken'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[5]|max_length[12]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Password must have at least 5 characters in length',
                    'max_length' => 'Password must not have characters more than 1 in length'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|min_length[5]|max_length[12]|matches[password]',
                'errors' => [
                    'required' => 'Confirm password is required',
                    'min_length' => 'Confirm password must have at least 5 characters in length',
                    'max_length' => 'Confirm password must not have characters more than 12 in length',
                    'matches' => 'Confirm password not matches to password'
                ]
            ]
        ]);

        if (!$validation) {
            return view('auth/register', [
                'validation' => $this->validator
            ]);
        } else {
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $user = [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ];

            $userModel = new UserModel();
            $query = $userModel->insert($user);
            if (!$query) {
                return redirect()->back()->with('fail', 'Something went wrong');
                // return redirect()->to('register')->with('fail', 'Something went wrong');
            } else {
                return redirect()->back()->with('success', 'You are now registered successfully');
            }
        }
    }

    public function logout()
    {
        if (session()->has('loggedUser')) {
            session()->remove('loggedUser');
            return redirect()->to('auth/login');
        }
    }
}