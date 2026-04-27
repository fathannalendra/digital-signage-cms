<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Media extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth');
        $this->load->database();
        $this->load->library('upload');
    }

    public function index()
    {
        // Tangkap Filter & Folder ID
        $filter_type = $this->input->get('filter'); // video, image, pdf, folder
        $current_folder_id = $this->input->get('folder_id') ? $this->input->get('folder_id') : 0;

        // Default Data
        $data['folders'] = [];
        $data['media_list'] = [];

        // Ambil semua folder untuk Dropdown Edit (Pindah Folder)
        $data['all_folders_list'] = $this->db->get('media_folders')->result();

        $this->db->where('file_type', 'pdf');
        $this->db->order_by('id', 'DESC');
        $data['existing_pdfs'] = $this->db->get('media')->result();
        // --- LOGIKA FILTER UTAMA ---

        if ($filter_type == 'video') {
            // CASE 1: GLOBAL VIDEO
            $this->db->where('file_type', 'video');
            $this->db->order_by('id', 'DESC');
            $data['media_list'] = $this->db->get('media')->result();
            $data['current_folder_name'] = 'Filter: Semua Video';
        } elseif ($filter_type == 'image') {
            // CASE 2: GLOBAL IMAGE
            $this->db->where('file_type', 'image');
            $this->db->order_by('id', 'DESC');
            $data['media_list'] = $this->db->get('media')->result();
            $data['current_folder_name'] = 'Filter: Semua Gambar';
        } elseif ($filter_type == 'pdf') {
            // CASE 3: GLOBAL PDF
            $this->db->where('file_type', 'pdf');
            $this->db->order_by('id', 'DESC');
            $data['media_list'] = $this->db->get('media')->result();
            $data['current_folder_name'] = 'Filter: Semua PDF';
        } elseif ($filter_type == 'folder') {
            // CASE 4: HANYA FOLDER
            $data['folders'] = $this->db->get('media_folders')->result();
            $data['current_folder_name'] = 'Filter: Daftar Folder';
        } else {
            // CASE 5: MODE "SEMUA" / NAVIGASI NORMAL

            // A. JIKA DI HALAMAN UTAMA (ROOT)
            if ($current_folder_id == 0) {
                // 1. Tampilkan Semua Folder
                $data['folders'] = $this->db->get('media_folders')->result();

                // 2. Tampilkan SEMUA FILE (Tanpa peduli dia di dalam folder atau tidak)
                // Hapus baris "where folder_id" agar semua file muncul
                $this->db->order_by('id', 'DESC');
                $data['media_list'] = $this->db->get('media')->result();

                $data['current_folder_name'] = 'Semua Media';
            }
            // B. JIKA SEDANG MEMBUKA FOLDER SPESIFIK
            else {
                // Di dalam folder, kita hanya tampilkan isi folder itu saja
                $this->db->where('folder_id', $current_folder_id);
                $this->db->order_by('id', 'DESC');
                $data['media_list'] = $this->db->get('media')->result();

                // Ambil nama folder untuk judul
                $folder_info = $this->db->get_where('media_folders', ['id' => $current_folder_id])->row();
                $data['current_folder_name'] = $folder_info ? $folder_info->name : 'Folder';
            }
        }

        // Kirim data tambahan ke View
        $data['current_filter'] = $filter_type;
        $data['current_folder_id'] = $current_folder_id;

        $data['content_view'] = 'admin/media_view';
        $this->load->view('admin/layout_template', $data);
    }

    public function create_folder()
    {
        $name = $this->input->post('folder_name');
        if ($name) {
            $this->db->insert('media_folders', ['name' => $name]);
            $this->session->set_flashdata('success', 'Folder berhasil dibuat.');
        }
        redirect('media');
    }

    public function upload()
    {

        // $filename = $_FILES['userfile']['name'];
        // $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        //  TENTUKAN FOLDER TUJUAN (Pastikan folder ini SUDAH DIBUAT)
        $filename = $_FILES['userfile']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // UPDATE 1: Deteksi PDF & Folder Tujuan Fisik
        if (in_array($ext, ['mp4', 'mkv', 'webm', 'avi'])) {
            $subfolder = 'uploads_video/';
            $type = 'video';
            $default_duration = 60;
        } elseif ($ext == 'pdf') {  // <-- TAMBAHAN PDF
            $subfolder = 'uploads_pdf/';
            $type = 'pdf';
            $default_duration = 0; // PDF tidak butuh durasi play
        } else {
            $subfolder = 'uploads_image/';
            $type = 'image';
            $default_duration = 5;
        }

        // KONFIGURASI UPLOAD
        $config['upload_path']   = FCPATH . 'uploads/' . $subfolder;


        $config['allowed_types'] = '*';

        $config['max_size']      = 500000;
        $config['encrypt_name']  = TRUE; // Rename file jadi acak

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('userfile')) { // Tambahkan 'userfile' di dalam kurung
            $error = $this->upload->display_errors();
            $this->session->set_flashdata('error', $error);
            redirect('media');
        } else {

            $upload_data = $this->upload->data();

            $uploaded_ext = strtolower(pathinfo($upload_data['file_name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mkv', 'avi', 'webm', 'pdf'];

            if (!in_array($uploaded_ext, $allowed_exts)) {
                // Kalau file aneh-aneh (bukan gambar/video), HAPUS LANGSUNG!
                unlink($upload_data['full_path']);
                $this->session->set_flashdata('error', 'Format file tidak diizinkan! Hanya boleh Video & Gambar.');
            } else {

                $db_path = 'uploads/' . $subfolder . $upload_data['file_name'];

                $data_db = [
                    'title'     => $this->input->post('title'),
                    'caption'   => $this->input->post('caption'),
                    'file_path' => $db_path,
                    'file_type' => $type,
                    'duration'  => $this->input->post('duration') ? $this->input->post('duration') : $default_duration,
                    'folder_id' => $this->input->post('target_folder_id') // <-- PENTING: ID FOLDER
                ];

                $this->db->insert('media', $data_db);
                $this->session->set_flashdata('success', 'File berhasil diupload!');
            }
        }

        // REDIRECT 
        $redirect_playlist_id = $this->input->post('redirect_playlist_id');
        if ($redirect_playlist_id) {
            redirect('playlists/manage/' . $redirect_playlist_id);
        } else {
            redirect('media');
        }
    }

    public function update()
    {
        $id = $this->input->post('id');

        // (BARU) Tambahkan folder_id ke data yang diupdate
        $data = [
            'title'    => $this->input->post('title'),
            'caption'  => $this->input->post('caption'),
            'duration' => $this->input->post('duration'),
            'folder_id' => $this->input->post('folder_id') // <-- Logika Pindah Folder
        ];

        $this->db->where('id', $id);
        $this->db->update('media', $data);

        $this->session->set_flashdata('success', 'Data berhasil diperbarui.');

        // Redirect kembali ke folder tempat file itu berada sekarang
        redirect('media?folder_id=' . $this->input->post('folder_id'));
    }

    public function delete($id)
    {
        $media = $this->db->get_where('media', ['id' => $id])->row();
        if ($media) {
            $path = FCPATH . $media->file_path;
            if (file_exists($path)) unlink($path);

            $this->db->delete('media', ['id' => $id]);
            $this->db->delete('playlist_items', ['media_id' => $id]);
            $this->session->set_flashdata('success', 'Media dihapus.');
        }
        redirect('media');
    }

    // --- FUNGSI BARU: UPDATE NAMA FOLDER ---
    public function update_folder()
    {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $selected_pdf = $this->input->post('selected_pdf'); // Tangkap hasil pilihan dropdown

        if ($id && $name) {
            $data = [
                'name' => $name,
                'pdf_file' => $selected_pdf // Simpan path PDF ke kolom baru tadi
            ];

            $this->db->where('id', $id);
            $this->db->update('media_folders', $data);
            $this->session->set_flashdata('success', 'Folder berhasil diupdate.');
        }
        redirect('media');
    }

    // --- FUNGSI BARU: HAPUS FOLDER BESERTA ISINYA ---
    public function delete_folder($id)
    {
        // 1. Ambil semua file yang ada di dalam folder ini
        $files = $this->db->get_where('media', ['folder_id' => $id])->result();

        // 2. Hapus Fisik File di Server
        foreach ($files as $file) {
            $path = FCPATH . $file->file_path;
            if (file_exists($path)) {
                unlink($path); // Hapus file dari folder uploads
            }
        }

        // 3. Hapus Data File di Database
        $this->db->delete('media', ['folder_id' => $id]);

        // 4. Hapus Item Playlist (jika file ini pernah masuk playlist)
        // Kita loop lagi ID medianya untuk hapus di playlist_items
        foreach ($files as $file) {
            $this->db->delete('playlist_items', ['media_id' => $file->id]);
        }

        // 5. Terakhir, Hapus Foldernya Sendiri
        $this->db->delete('media_folders', ['id' => $id]);

        $this->session->set_flashdata('success', 'Folder dan seluruh isinya berhasil dihapus.');
        redirect('media');
    }

    // --- FUNGSI BARU: UPLOAD SATU FOLDER SEKALIGUS ---
    public function upload_folder_bulk()
    {
        // set_time_limit(0);
        // ini_set('memory_limit', '1024M');

        $folder_name = $this->input->post('new_folder_name');

        $this->db->insert('media_folders', ['name' => $folder_name]);
        $new_folder_id = $this->db->insert_id();


        $count = count($_FILES['folder_files']['name']);
        $success_count = 0;

        for ($i = 0; $i < $count; $i++) {

            if ($_FILES['folder_files']['error'][$i] != 0) continue;

            $filename = $_FILES['folder_files']['name'][$i];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));


            if (in_array($ext, ['mp4', 'mkv', 'webm', 'avi'])) {
                $subfolder = 'uploads_video/';
                $type = 'video';
                $duration = 60;
            } elseif ($ext == 'pdf') {
                $subfolder = 'uploads_pdf/';
                $type = 'pdf';
                $duration = 0;
            } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $subfolder = 'uploads_image/';
                $type = 'image';
                $duration = 5;
            } else {
                continue;
            }


            $target_dir = FCPATH . 'uploads/' . $subfolder;
            $new_filename = md5(uniqid(rand(), true)) . '.' . $ext; // Generate nama acak
            $target_file = $target_dir . $new_filename;
            $db_path = 'uploads/' . $subfolder . $new_filename;

            // Pindahkan file dari temp ke folder tujuan
            if (move_uploaded_file($_FILES['folder_files']['tmp_name'][$i], $target_file)) {

                // Simpan ke Database Media
                $data_db = [
                    'title'     => pathinfo($filename, PATHINFO_FILENAME), // Pakai nama asli file sbg judul
                    'caption'   => '',
                    'file_path' => $db_path,
                    'file_type' => $type,
                    'duration'  => $duration,
                    'folder_id' => $new_folder_id // Masukkan ke folder yang baru dibuat
                ];

                $this->db->insert('media', $data_db);
                $success_count++;
            }
        }

        $this->session->set_flashdata('success', 'Berhasil membuat folder "' . $folder_name . '" dan mengupload ' . $success_count . ' file.');
        redirect('media');
    }
}
