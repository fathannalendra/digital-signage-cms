<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth');
        $this->load->database();
    }

    public function index()
    {
        // Ambil Statistik Data untuk ditampilkan di kartu-kartu
        $data['total_media']    = $this->db->count_all('media');
        $data['total_playlist'] = $this->db->count_all('playlists');
        $data['total_tv']       = $this->db->count_all('tv_points');

        // Hitung jadwal yang SEDANG TAYANG hari ini
        $today = date('Y-m-d');
        $this->db->where('start_date <=', $today);
        $this->db->where('end_date >=', $today);
        $this->db->where('is_active', 1);
        $data['active_schedules'] = $this->db->count_all_results('schedules');

        // Load View Dashboard Baru
        $data['content_view'] = 'admin/dashboard_view';
        $this->load->view('admin/layout_template', $data);
    }
}
