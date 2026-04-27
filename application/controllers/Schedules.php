<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Schedules extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth');
        $this->load->database();
    }

    public function index()
    {
        $this->db->select('schedules.*, playlists.name as playlist_name');
        $this->db->from('schedules');
        $this->db->join('playlists', 'schedules.playlist_id = playlists.id');
        // Urutkan: Jadwal prioritas (ada tanggalnya) di atas, yang selamanya di bawah
        $this->db->order_by('schedules.end_date', 'DESC');
        $this->db->order_by('schedules.start_date', 'DESC');
        $data['schedules'] = $this->db->get()->result();

        $data['playlists'] = $this->db->get('playlists')->result();
        $data['tvs']       = $this->db->get('tv_points')->result();

        $data['content_view'] = 'admin/schedules_view';
        $this->load->view('admin/layout_template', $data);
    }

    public function create()
    {
        // PROSES TARGET TV
        $targets = $this->input->post('target_tv_id');
        if (!empty($targets)) {
            $clean_targets = array_diff($targets, ['0']);
            if (empty($clean_targets)) {
                $all_tvs = $this->db->select('id')->get('tv_points')->result_array();
                $target_string = implode(',', array_column($all_tvs, 'id'));
            } else {
                $target_string = implode(',', $clean_targets);
            }
        } else {
            $this->session->set_flashdata('error', 'Pilih minimal satu TV.');
            redirect('schedules');
            return;
        }

        // PROSES TANGGAL (LOGIKA SELAMANYA)
        $is_forever = $this->input->post('is_forever'); // Nilainya 1 atau NULL
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');

        if ($is_forever == 1) {
            $final_end_date = NULL;
        } else {
            $final_end_date = $end_date;
            if ($final_end_date < $start_date) {
                $this->session->set_flashdata('error', 'Tanggal Selesai tidak boleh mundur!');
                redirect('schedules');
                return;
            }
        }

        $data = [
            'playlist_id'  => $this->input->post('playlist_id'),
            'target_tv_id' => $target_string,
            'start_date'   => $start_date,
            'end_date'     => $final_end_date,
            'is_active'    => 1
        ];

        $this->db->insert('schedules', $data);
        $this->session->set_flashdata('success', 'Jadwal berhasil disimpan.');
        redirect('schedules');
    }

    public function edit($id)
    {
        $data['schedule'] = $this->db->get_where('schedules', ['id' => $id])->row();
        if (!$data['schedule']) redirect('schedules');

        $data['playlists'] = $this->db->get('playlists')->result();
        $data['tvs']       = $this->db->get('tv_points')->result();

        $data['content_view'] = 'admin/schedules_edit_view';
        $this->load->view('admin/layout_template', $data);
    }

    public function update($id)
    {
        // 1. PROSES TV
        $targets = $this->input->post('target_tv_id');
        if (!empty($targets)) {
            $clean_targets = array_diff($targets, ['0']);
            $target_string = empty($clean_targets) ? '' : implode(',', $clean_targets);
        } else {
            $target_string = '';
        }

        // 2. PROSES TANGGAL
        $is_forever = $this->input->post('is_forever');
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');

        if ($is_forever == 1) {
            $final_end_date = NULL;
        } else {
            $final_end_date = $end_date;
            if ($final_end_date < $start_date) {
                $this->session->set_flashdata('error', 'Tanggal error.');
                redirect('schedules/edit/' . $id);
                return;
            }
        }

        $data = [
            'playlist_id'  => $this->input->post('playlist_id'),
            'target_tv_id' => $target_string,
            'start_date'   => $start_date,
            'end_date'     => $final_end_date
        ];

        $this->db->where('id', $id);
        $this->db->update('schedules', $data);

        $this->session->set_flashdata('success', 'Jadwal diperbarui.');
        redirect('schedules');
    }

    public function delete($id)
    {
        $this->db->delete('schedules', ['id' => $id]);
        $this->session->set_flashdata('success', 'Jadwal dihapus.');
        redirect('schedules');
    }

    public function toggle_status($id, $current_status)
    {
        $new_status = ($current_status == 1) ? 0 : 1;
        $this->db->where('id', $id);
        $this->db->update('schedules', ['is_active' => $new_status]);
        redirect('schedules');
    }
}
