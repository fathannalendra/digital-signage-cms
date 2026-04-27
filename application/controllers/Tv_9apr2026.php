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
}
