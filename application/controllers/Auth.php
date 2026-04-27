<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->database();
        $this->load->helper('url');
    }

    public function index()
    {

        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        $this->load->view('admin/login_view');
    }

    public function process()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');


        $user = $this->db->get_where('users', ['username' => $username])->row();


        if ($user) {

            if (password_verify($password, $user->password)) {
                $session_data = [
                    'user_id'   => $user->id,
                    'username'  => $user->username,
                    'role'      => $user->role,
                    'logged_in' => TRUE
                ];
                $this->session->set_userdata($session_data);

                redirect('dashboard');
            } else {
                $this->session->set_flashdata('error', 'Password salah!');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('error', 'Username tidak ditemukan!');
            redirect('auth');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }
}
