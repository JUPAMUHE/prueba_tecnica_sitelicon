<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Importar la librería de Stripe
require_once APPPATH.'../vendor/autoload.php';

class Checkout extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Order_model');
    

        $this->load->config('config');
        $this->load->helper('url');
        // Configurar la clave secreta de Stripe desde el archivo de configuración
        \Stripe\Stripe::setApiKey($this->config->item('stripe_api_key'));
    }

    public function index()
    {
        // Cargar el formulario de pago con la clave pública de Stripe
        $this->load->view('checkout_form', [
            'stripe_publishable_key' => $this->config->item('stripe_publishable_key')
        ]);
    }

    public function process_payment()
    {
        $token = $this->input->post('stripeToken');
        $total_amount = $this->input->post('total_amount'); 

        $user_id = 1; // Asume que el usuario está autenticado y tiene ID 1

        try {
            // Crear el cargo usando la API de Stripe
            $charge = \Stripe\Charge::create([
                'amount' => $total_amount, 
                'currency' => 'usd',
                'description' => 'Ejemplo de compra',
                'source' => $token,
            ]);


            $this->session->set_flashdata('success', '¡Pago realizado con éxito!');
            $this->Order_model->create_order($user_id, $total_amount,'success');

            redirect('payment_success');

        } catch (\Stripe\Exception\CardException $e) {
            $this->session->set_flashdata('error', 'Error en el pago: ' . $e->getError()->message);
            $this->Order_model->create_order($user_id, $total_amount,'Rechazado');
            redirect('payment_failure');
        } catch (Exception $e) {
            // Manejar cualquier otro tipo de excepción
            $this->session->set_flashdata('error', 'Ocurrió un error inesperado.');
            redirect('payment_failure');
        }
    }

    public function payment_success() {
        // Cargar vista de éxito
        $this->load->view('payment_success');
    }

    public function payment_failure() {
        // Cargar vista de fallo
        $this->load->view('payment_failure');
    }
}
