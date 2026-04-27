<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Player_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function index()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $client_tv_id = $this->input->get('id');
        if (!$client_tv_id) $client_tv_id = 0;

        // 1. Cari Jadwal Aktif
        $schedule = $this->db->order_by('id', 'DESC')
            ->get_where('schedules', ['target_tv_id' => $client_tv_id, 'is_active' => 1])
            ->row();

        $playlist_data = [];
        $tv_name = "Display TV";

        $tv_info = $this->db->get_where('tv_points', ['id' => $client_tv_id])->row();
        if ($tv_info) $tv_name = $tv_info->name;

        if ($schedule) {
            // 2. QUERY UTAMA (Ditambah media_folders.name)
            $this->db->select('playlist_items.sort_order, media.file_path, media.file_type, media.duration, media.title, media.caption, media.folder_id, media_folders.pdf_file, media_folders.name as folder_name');

            $this->db->from('playlist_items');
            $this->db->join('media', 'playlist_items.media_id = media.id');
            $this->db->join('media_folders', 'media.folder_id = media_folders.id', 'left');

            $this->db->where('playlist_items.playlist_id', $schedule->playlist_id);
            $this->db->order_by('playlist_items.sort_order', 'ASC');

            $query = $this->db->get();

            foreach ($query->result() as $row) {
                // Link PDF Logic
                $pdf_full_url = "";
                $pdf_relative = "";

                if (!empty($row->pdf_file)) {
                    $pdf_full_url = base_url($row->pdf_file);
                    $pdf_relative = $row->pdf_file;
                }

                $playlist_data[] = [
                    'type'         => $row->file_type,
                    'src'          => base_url($row->file_path),
                    'duration'     => $row->duration,
                    'caption'      => $row->caption,
                    'folder_id'    => $row->folder_id,
                    'folder_name'  => $row->folder_name, // <-- INI YANG BARU
                    'pdf_full_url' => $pdf_full_url,
                    'pdf_relative' => $pdf_relative
                ];
            }
        }

       $playlist_id = $schedule ? $schedule->playlist_id : null; 

        
        echo json_encode([
            "status"      => "success",
            "tv_identity" => $tv_name,
            "playlist_id" => $playlist_id, 
            "data"        => $playlist_data
        ]);
    }

    public function get_locations()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        $this->db->select('id, name, location');
        $tvs = $this->db->get('tv_points')->result();
        echo json_encode($tvs);
    }
}
