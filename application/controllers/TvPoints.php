<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TvPoints extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth');
        $this->load->database();
    }

    public function index()
    {


        $data['tvs'] = $this->db->get('tv_points')->result();
        $data['content_view'] = 'admin/tv_points_view';
        $this->load->view('admin/layout_template', $data);
    }

    public function add()
    {
        $data = [
            'name' => $this->input->post('name'),
            'location' => $this->input->post('location')
        ];
        $this->db->insert('tv_points', $data);
        $this->session->set_flashdata('success', 'Titik TV baru ditambahkan.');
        redirect('TvPoints');
    }

    // Fungsi untuk update data TV
    public function update()
    {
        $id = $this->input->post('id');

        $data = [
            'name' => $this->input->post('name'),
            'location' => $this->input->post('location')
        ];

        $this->db->where('id', $id);
        $this->db->update('tv_points', $data);

        redirect('TvPoints'); // Kembali ke halaman list
    }

    public function delete($id)
    {
        $this->db->delete('tv_points', ['id' => $id]);

        // Hapus jadwal yang terkait TV ini agar tidak error (opsional)
        $this->db->delete('schedules', ['target_tv_id' => $id]);

        $this->session->set_flashdata('success', 'Titik TV dihapus.');
        redirect('TvPoints');
    }
}
