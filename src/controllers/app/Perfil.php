<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Perfil extends CI_Controller
{


    private $accessoPermitido;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'my_tag_helper'));
        $this->load->model('offerjobeloquent');
        $this->load->model('postulatejobeloquent');
        $this->load->model('usereloquent');
        $this->form_validation->set_message('no_repetir_email', 'Existe otro registro con el mismo %s');
        /**
         * En caso se defina el campo mobile como único, validaremos si ya se registró anteriormente
         */
        $this->form_validation->set_message('no_repetir_mobile', 'Existe otro registro con el mismo %s');
    }


	public function index()
    {
        if ($this->session->userdata('user_rol') != NULL) {
            $data['pagina'] =  getenv('TEMPLATE_THEME').'/app/perfil_view';
            $data['perfil'] = Usereloquent::findOrFail($this->session->userdata('user_id'));
            $data['document_type'] = Usereloquent::getListDocumentType();
            $data['career'] = Usereloquent::getListCareers();
            $this->load->view(getenv('TEMPLATE_THEME').'/app/app_view', $data);
        } else {
            $this->session->set_flashdata('error', '');
            redirect('/wp-login');
        }
    }

public function no_repetir_email($registro)
    {
        $registro = $this->input->post();
        $usuario = UserEloquent::getUserBy('email', $registro['email']);
        if ($usuario and (!isset($registro['id']) or ($registro['id'] != $usuario->id))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * En caso se defina el campo mobile como único, validaremos si ya se registró anteriormente
     */
    public function no_repetir_mobile($registro)
    {
        $registro = $this->input->post();
        $usuario = UserEloquent::getUserBy('mobile', $registro['mobile']);
        if ($usuario and (!isset($registro['id']) or ($registro['id'] != $usuario->id))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function actualizaPerfil()
    {
        $registro = $this->input->post();
        $this->form_validation->set_rules('email', 'Email', 'valid_email|callback_no_repetir_email');
        $this->form_validation->set_rules('mobile', 'teléfono celular', 'required|callback_no_repetir_mobile');
        //si el proceso falla mostramos errores
        if ($this->form_validation->run() == FALSE) {
            $this->index();
            //en otro caso procesamos los datos
        } else {

            date_default_timezone_set('America/Lima');
            if ($this->session->userdata('user_rol') != NULL) {
                $id = $this->session->userdata('user_id');
                $data = array(
                    'mobile' => $this->input->post('mobile', true),
                    'email' => $this->input->post('email', true),
                    'address' => $this->input->post('address', true)
                );
                $model = UserEloquent::findOrFail($id);
                $model->fill($data);
                $model->save($data);
                if ($this->session->userdata('user_email') != $data['email']) {
                    $this->session->set_userdata('user_email', $data['email']);
                }
                // Display success message
                $this->session->set_flashdata('flashSuccess', 'Actualización exitosa.');
                redirect('/users/perfil');
            } else {
                $this->index();
            }
        }
    }
}
