<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('db_model');
		$this->load->helper('url');
	}
	public function profile(){
		$username = $this->session->userdata('username'); 
		$status = $this->session->userdata('status');

		if($username != null && $status == 'o'){
			$data['admin'] = $this->db_model->get_admin($username); 
			$this->load->view('templates/haut');
			$this->load->view('templates/menu_admin.php');
			$this->load->view('admin-profile',$data);
			$this->load->view('templates/bas'); 
		}else{
			redirect(base_url().'index.php/compte/connecter');
		}
	}
	public function modifier(){
		$username = $this->session->userdata('username');
		$status = $this->session->userdata('status');

		if($username == null&& $status == 'o'){
			redirect(base_url().'index.php/compte/connecter');
		}else{		
			$this->load->helper('form');
			$this->load->library('form_validation');
			$data['admin'] = $this->db_model->get_admin($username);
			// $this->form_validation->set_rules('mdp', 'mdp', 'required');
			// $this->form_validation->set_rules('cnfmdp', 'cnfmdp', 'required');

			if ($this->input->post('mdp') == null && $this->input->post('cnfmdp') == null){ 
				$data['error'] = null;  
				$this->load->view('templates/haut');
				$this->load->view('templates/menu_admin.php');
				$this->load->view('admin-modifier',$data);
				$this->load->view('templates/bas');
			}else if($this->input->post('mdp') != null && $this->input->post('cnfmdp') != null){
				$cnfmdp = $this->input->post('cnfmdp');
				$mdp = $this->input->post('mdp');

				if($cnfmdp != $mdp){
					$data['error'] = 'Confirmation du mot de passe erronée, veuillez réessayer !'; 
					$this->load->view('templates/haut');
					$this->load->view('templates/menu_admin.php');
					$this->load->view('admin-modifier',$data);
					$this->load->view('templates/bas');
				}else{
					$salt = "MY_sel@1999";
					$password = hash('sha256', $salt.$mdp);
					$username = $this->session->userdata('username');
					$res = $this->db_model->update_compte($username,$password);
					if($res){
						redirect(base_url().'index.php/admin/profile/');
					}else{
						$data['error'] = 'Erreur inconnu, Vous pouvez réessayer plus tard !'; 
						$this->load->view('templates/haut');
						$this->load->view('templates/menu_admin.php');
						$this->load->view('admin-modifier',$data);
						$this->load->view('templates/bas');
					}	
				}
			}else{
				$data['error'] = 'Champs de saisie vides !'; 
				$this->load->view('templates/haut');
				$this->load->view('templates/menu_admin.php');
				$this->load->view('admin-modifier',$data);
				$this->load->view('templates/bas');
			}
		}
	}
}
?>