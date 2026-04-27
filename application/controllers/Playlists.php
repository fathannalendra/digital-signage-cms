<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Playlists extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth');
        $this->load->database();
    }

    public function index()
    {
        $data['playlists'] = $this->db->get('playlists')->result();
        $data['content_view'] = 'admin/playlists_view';
        $this->load->view('admin/layout_template', $data);
    }

    public function create()
    {
        $data = [
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description')
        ];
        $this->db->insert('playlists', $data);
        $this->session->set_flashdata('success', 'Playlist dibuat.');
        redirect('playlists');
    }

    public function update()
    {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $desc = $this->input->post('description');

        if ($id && $name) {
            $data = [
                'name' => $name,
                'description' => $desc
            ];
            $this->db->where('id', $id);
            $this->db->update('playlists', $data);
            $this->session->set_flashdata('success', 'Playlist berhasil diperbarui.');
        }
        redirect('playlists');
    }

    public function delete($id)
    {
        $this->db->delete('playlists', ['id' => $id]);
        $this->session->set_flashdata('success', 'Playlist dihapus.');
        redirect('playlists');
    }



    public function manage_backup9jan26($playlist_id)
    {
        $data['playlist'] = $this->db->get_where('playlists', ['id' => $playlist_id])->row();
        if (!$data['playlist']) redirect('playlists');

        $this->db->select('playlist_items.id as item_id, playlist_items.sort_order, media.*');
        $this->db->from('playlist_items');
        $this->db->join('media', 'playlist_items.media_id = media.id');
        $this->db->where('playlist_items.playlist_id', $playlist_id);
        $this->db->order_by('playlist_items.sort_order', 'ASC');
        $data['existing_items'] = $this->db->get()->result();

        $data['all_media'] = $this->db->order_by('id', 'DESC')->get('media')->result();
        $data['content_view'] = 'admin/playlists_manage_view';


        $mode = $this->input->get('mode');
        if ($mode == 'simple') {
            $this->load->view('admin/layout_simple', $data);
        } else {
            $this->load->view('admin/layout_template', $data);
        }
    }

    public function manage_backup27feb26($playlist_id)
    {
        $data['playlist'] = $this->db->get_where('playlists', ['id' => $playlist_id])->row();
        if (!$data['playlist']) redirect('playlists');

        // 1. AMBIL ITEM PLAYLIST (KANAN) + NAMA FOLDER
        // Kita join ke tabel media_folders untuk dapat namanya
        $this->db->select('playlist_items.id as item_id, playlist_items.sort_order, media.*, media_folders.name as folder_name');
        $this->db->from('playlist_items');
        $this->db->join('media', 'playlist_items.media_id = media.id');
        $this->db->join('media_folders', 'media.folder_id = media_folders.id', 'left'); // <-- JOIN PENTING
        $this->db->where('playlist_items.playlist_id', $playlist_id);
        $this->db->order_by('playlist_items.sort_order', 'ASC');
        $data['existing_items'] = $this->db->get()->result();

        // 2. AMBIL FOLDER (DROPDOWN)
        $data['folders'] = $this->db->get('media_folders')->result();

        // 3. AMBIL MEDIA TERSEDIA (KIRI) + NAMA FOLDER
        $selected_folder_id = $this->input->get('folder_id');
        
        $this->db->select('media.*, media_folders.name as folder_name'); // <-- SELECT NAME
        $this->db->from('media');
        $this->db->join('media_folders', 'media.folder_id = media_folders.id', 'left'); // <-- JOIN JUGA DISINI
        
        $this->db->order_by('media.id', 'DESC');
        if ($selected_folder_id) {
            $this->db->where('media.folder_id', $selected_folder_id);
        }
        $data['all_media'] = $this->db->get()->result();
        
        $data['selected_folder_id'] = $selected_folder_id;
        $data['content_view'] = 'admin/playlists_manage_view';

        $mode = $this->input->get('mode');
        if ($mode == 'simple') {
            $this->load->view('admin/layout_simple', $data);
        } else {
            $this->load->view('admin/layout_template', $data);
        }
    }

    public function manage($playlist_id)
    {
        $data['playlist'] = $this->db->get_where('playlists', ['id' => $playlist_id])->row();
        if (!$data['playlist']) redirect('playlists');

        // 1. AMBIL ITEM PLAYLIST (KANAN) + NAMA FOLDER
        // Kita join ke tabel media_folders untuk dapat namanya
        $this->db->select('playlist_items.id as item_id, playlist_items.sort_order, media.*, media_folders.name as folder_name');
        $this->db->from('playlist_items');
        $this->db->join('media', 'playlist_items.media_id = media.id');
        $this->db->join('media_folders', 'media.folder_id = media_folders.id', 'left'); // <-- JOIN PENTING
        $this->db->where('playlist_items.playlist_id', $playlist_id);
        $this->db->order_by('playlist_items.sort_order', 'ASC');
        $data['existing_items'] = $this->db->get()->result();

        // 2. AMBIL FOLDER (DROPDOWN)
        $data['folders'] = $this->db->get('media_folders')->result();

      // 3. AMBIL MEDIA TERSEDIA (KIRI) + NAMA FOLDER
        $selected_folder_id = $this->input->get('folder_id');
        
        $this->db->select('media.*, media_folders.name as folder_name');
        $this->db->from('media');
        $this->db->join('media_folders', 'media.folder_id = media_folders.id', 'left');
        
        // --- TERAPKAN NATURAL SORTING DISINI ---
        $this->db->order_by('LENGTH(media.title)', 'ASC');
        $this->db->order_by('media.title', 'ASC');
        
        if ($selected_folder_id) {
            $this->db->where('media.folder_id', $selected_folder_id);
        }
        $data['all_media'] = $this->db->get()->result();
        
        $data['selected_folder_id'] = $selected_folder_id;
        $data['content_view'] = 'admin/playlists_manage_view';

        $mode = $this->input->get('mode');
        if ($mode == 'simple') {
            $this->load->view('admin/layout_simple', $data);
        } else {
            $this->load->view('admin/layout_template', $data);
        }
    }

    public function add_folder_items($playlist_id, $folder_id)
    {
        // 1. Ambil semua file di folder tersebut dan URUTKAN DENGAN NATURAL SORTING
        $this->db->where('folder_id', $folder_id);
        $this->db->order_by('LENGTH(title)', 'ASC'); // Urutkan panjang karakter dulu
        $this->db->order_by('title', 'ASC');         // Baru urutkan abjad
        $files = $this->db->get('media')->result();

        if (empty($files)) {
            $this->session->set_flashdata('error', 'Folder ini kosong.');
            redirect('playlists/manage/' . $playlist_id . '?folder_id=' . $folder_id);
        }

        // 2. Cek urutan terakhir di playlist ini
        $last = $this->db->query("SELECT MAX(sort_order) as max_sort FROM playlist_items WHERE playlist_id = $playlist_id")->row();
        $current_sort = ($last->max_sort) ? $last->max_sort : 0;

        // 3. Looping insert
        $count = 0;
        foreach ($files as $file) {
            $current_sort++; // Naikkan urutan

            $data = [
                'playlist_id' => $playlist_id,
                'media_id'    => $file->id,
                'sort_order'  => $current_sort
            ];
            $this->db->insert('playlist_items', $data);
            $count++;
        }

        $this->session->set_flashdata('success', $count . ' media dari folder berhasil ditambahkan.');
        redirect('playlists/manage/' . $playlist_id . '?folder_id=' . $folder_id);
    }
    
    // --- FUNCTION BARU: MASUKKAN SATU FOLDER KE PLAYLIST ---
    public function add_folder_items_backup27feb26($playlist_id, $folder_id)
    {
        // 1. Ambil semua file di folder tersebut
        $files = $this->db->get_where('media', ['folder_id' => $folder_id])->result();

        if (empty($files)) {
            $this->session->set_flashdata('error', 'Folder ini kosong.');
            redirect('playlists/manage/' . $playlist_id . '?folder_id=' . $folder_id);
        }

        // 2. Cek urutan terakhir di playlist ini
        $last = $this->db->query("SELECT MAX(sort_order) as max_sort FROM playlist_items WHERE playlist_id = $playlist_id")->row();
        $current_sort = ($last->max_sort) ? $last->max_sort : 0;

        // 3. Looping insert
        $count = 0;
        foreach ($files as $file) {
            $current_sort++; // Naikkan urutan

            $data = [
                'playlist_id' => $playlist_id,
                'media_id'    => $file->id,
                'sort_order'  => $current_sort
            ];
            $this->db->insert('playlist_items', $data);
            $count++;
        }

        $this->session->set_flashdata('success', $count . ' video dari folder berhasil ditambahkan.');
        redirect('playlists/manage/' . $playlist_id . '?folder_id=' . $folder_id);
    }

    public function add_item($playlist_id, $media_id)
    {
        $last = $this->db->query("SELECT MAX(sort_order) as max_sort FROM playlist_items WHERE playlist_id = $playlist_id")->row();
        $new_sort = ($last->max_sort) + 1;

        $data = [
            'playlist_id' => $playlist_id,
            'media_id'    => $media_id,
            'sort_order'  => $new_sort
        ];

        $this->db->insert('playlist_items', $data);
        $this->session->set_flashdata('success', 'Video berhasil ditambahkan.');
        redirect('playlists/manage/' . $playlist_id);
    }

    public function remove_item($item_id, $playlist_id)
    {
        $this->db->delete('playlist_items', ['id' => $item_id]);
        $this->session->set_flashdata('success', 'Video dihapus.');
        redirect('playlists/manage/' . $playlist_id);
    }

    public function update_order($playlist_id)
    {
        $items = $this->input->post('items');
        if ($items) {
            foreach ($items as $item_id => $sort_order) {
                $this->db->where('id', $item_id);
                $this->db->update('playlist_items', ['sort_order' => $sort_order]);
            }
            $this->session->set_flashdata('success', 'Urutan berhasil disimpan.');
        }
        redirect('playlists/manage/' . $playlist_id);
    }


    public function play($id)
    {
        // Ambil Nama Playlist
        $data['playlist'] = $this->db->get_where('playlists', ['id' => $id])->row();

        // Ambil Isi Playlist
        $this->db->select('media.file_path, media.file_type, media.duration, media.title');
        $this->db->from('playlist_items');
        $this->db->join('media', 'playlist_items.media_id = media.id');
        $this->db->where('playlist_items.playlist_id', $id);
        $this->db->order_by('playlist_items.sort_order', 'ASC');
        $items = $this->db->get()->result();

        // Siapkan Data JSON untuk Javascript
        $clean_items = [];
        foreach ($items as $item) {
            $clean_items[] = [
                'src' => base_url($item->file_path),
                'type' => $item->file_type,
                'duration' => $item->duration,
                'title' => $item->title
            ];
        }

        $data['items_json'] = json_encode($clean_items);


        $this->load->view('admin/playlist_player', $data);
    }

    // --- FUNGSI BARU: HAPUS BANYAK ITEM SEKALIGUS ---
    public function bulk_delete_items($playlist_id)
    {
        $selected_ids = $this->input->post('selected_items'); // Ambil array ID dari checkbox
        
        if ($selected_ids && is_array($selected_ids)) {
            $this->db->where_in('id', $selected_ids);
            $this->db->delete('playlist_items');
            
            $count = count($selected_ids);
            $this->session->set_flashdata('success', "$count item berhasil dihapus dari playlist.");
        } else {
            $this->session->set_flashdata('error', 'Tidak ada item yang dipilih untuk dihapus.');
        }
        
        redirect('playlists/manage/' . $playlist_id);
    }
}
