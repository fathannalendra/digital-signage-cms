<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tv extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    public function index()
    {
        $data['tv_list'] = $this->db->get('tv_points')->result();
        $this->load->view('tv_display', $data);
    }

    // --- FUNGSI BARU UNTUK MENCATAT LAPORAN TAYANG ---
    public function log_play()
    {
        $playlist_id = $this->input->post('playlist_id');
        $tv_id       = $this->input->post('tv_id');
        $today       = date('Y-m-d');

        if ($playlist_id && $tv_id) {
            // Cek apakah hari ini TV ini sudah pernah memutar playlist ini?
            $this->db->where('playlist_id', $playlist_id);
            $this->db->where('tv_id', $tv_id);
            $this->db->where('date', $today);
            $cek = $this->db->get('playlist_stats')->row();

            if ($cek) {
                // Kalau HARI INI sudah ada datanya, tambahkan angkanya +1
                $this->db->set('play_count', 'play_count+1', FALSE);
                $this->db->where('id', $cek->id);
                $this->db->update('playlist_stats');
            } else {
                // Kalau HARI INI belum ada, buat baris baru
                $data = [
                    'playlist_id' => $playlist_id,
                    'tv_id'       => $tv_id,
                    'date'        => $today,
                    'play_count'  => 1
                ];
                $this->db->insert('playlist_stats', $data);
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Tercatat']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }
    }
}