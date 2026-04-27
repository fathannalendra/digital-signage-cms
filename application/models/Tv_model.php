<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tv_model extends CI_Model {

    public function get_playlist_for_tv($client_tv_id)
    {
        $today = date('Y-m-d');
        
        
        $this->db->where('is_active', 1);
        $this->db->where('start_date <=', $today);
        $this->db->order_by('id', 'DESC'); // Prioritas jadwal baru
        $candidates = $this->db->get('schedules')->result();

        $found_schedule = null;

        
        foreach ($candidates as $sch) {
            // Cek Tanggal Selesai (Handle NULL / 0000-00-00)
            $end_date = $sch->end_date;
            $is_forever = ($end_date == NULL || $end_date == '' || $end_date == '0000-00-00' || $end_date == '0000-00-00 00:00:00');
            
            // Kalau jadwal sudah lewat tanggalnya -> Skip
            if (!$is_forever && $end_date < $today) {
                continue; 
            }

            // Cek Target TV (Apakah ID TV ini boleh main?)
            $target_ids = explode(',', $sch->target_tv_id);
            if (in_array($client_tv_id, $target_ids) || in_array('0', $target_ids)) {
                $found_schedule = $sch;
                break; // KETEMU!
            }
        }

        // JIKA JADWAL KETEMU -> AMBIL ISI VIDEO
        if ($found_schedule) {
            $playlist_id = $found_schedule->playlist_id;
            
            $this->db->select('media.title, media.caption, media.file_path as src, media.file_type as type, media.duration');
            $this->db->from('playlist_items');
            $this->db->join('media', 'playlist_items.media_id = media.id');
            $this->db->where('playlist_items.playlist_id', $playlist_id);
            $this->db->order_by('playlist_items.sort_order', 'ASC');
            $items = $this->db->get()->result();

            $type_sch = (empty($found_schedule->end_date)) ? "Selamanya" : "Spesifik";
            
            // Return data lengkap
            return [
                'source' => "Jadwal Aktif ID: " . $found_schedule->id . " (" . $type_sch . ")",
                'data'   => $items
            ];
        }

        // JIKA TIDAK ADA JADWAL -> KEMBALIKAN KOSONG (STANDBY)
        return [
            'source' => "Menunggu Jadwal...",
            'data'   => []
        ];
    }

    
}