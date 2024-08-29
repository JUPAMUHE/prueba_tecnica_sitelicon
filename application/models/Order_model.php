<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function create_order($user_id, $total_amount,$payment_status) {
        $data = array(
            'user_id' => $user_id,
            'total_amount' => $total_amount,
            'payment_status' => $payment_status
        );

        $this->db->insert('orders', $data);
        return $this->db->insert_id();
    }

    public function update_payment_status($order_id, $status) {
        $this->db->where('id', $order_id);
        return $this->db->update('orders', array('payment_status' => $status, 'updated_at' => date('Y-m-d H:i:s')));
    }

    public function get_order($order_id) {
        return $this->db->get_where('orders', array('id' => $order_id))->row();
    }
}
