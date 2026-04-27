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

        // PERBAIKAN: Hitung jadwal SEDANG TAYANG hari ini menggunakan Raw SQL
    
        $sql = "SELECT COUNT(*) as total 
                FROM schedules 
                WHERE start_date <= CURDATE() 
                AND (
                    end_date >= CURDATE() 
                    OR end_date IS NULL 
                    OR CAST(end_date AS CHAR) = '0000-00-00' 
                    OR CAST(end_date AS CHAR) = ''
                )
                AND is_active = 1"; 
        
        $query = $this->db->query($sql);
        $data['active_schedules'] = $query->row()->total;

        // Ambil Total Seluruh Penayangan
        $this->db->select_sum('play_count');
        $query_stats = $this->db->get('playlist_stats');
        $total_plays = $query_stats->row()->play_count;
        $data['total_play_count'] = $total_plays ? $total_plays : 0; // Jika kosong, set 0

        // 1. Tangkap parameter tanggal dari URL (jika ada), kalau kosong gunakan hari ini
        $filter_date = $this->input->get('date');
        if (empty($filter_date)) {
            $filter_date = date('Y-m-d'); 
        }
        $data['filter_date'] = $filter_date; // Kirim ke View untuk ditampilkan di kotak input

        // 2. Tarik data dari database sesuai tanggal yang difilter
        // Asumsi nama tabel Mas adalah 'playlists' dan 'tv_points'
        $this->db->select('playlist_stats.play_count, playlists.name as playlist_name, tv_points.name as tv_name, tv_points.location as tv_location');
        $this->db->from('playlist_stats');
        
        // Gunakan LEFT JOIN agar jika playlist/tv terhapus, data laporannya tetap muncul
        $this->db->join('playlists', 'playlist_stats.playlist_id = playlists.id', 'left');
        $this->db->join('tv_points', 'playlist_stats.tv_id = tv_points.id', 'left');
        
        $this->db->where('playlist_stats.date', $filter_date);
        $this->db->order_by('playlist_stats.play_count', 'DESC'); // Urutkan dari tayangan terbanyak
        
        $data['daily_reports'] = $this->db->get()->result();

        // Load View Dashboard Baru
        $data['content_view'] = 'admin/dashboard_view';
        $this->load->view('admin/layout_template', $data);
    }
}