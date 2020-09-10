<?php
class ControllerExtensionPaymentSberBank extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/sberbank');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('sberbank', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_test'] = $this->language->get('text_test');
		$data['text_production'] = $this->language->get('text_production');

		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_mode'] = $this->language->get('entry_mode');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_order_success_status'] = $this->language->get('entry_order_success_status');
		$data['entry_order_reversed_status'] = $this->language->get('entry_order_reversed_status');
		$data['entry_order_processing_status'] = $this->language->get('entry_order_processing_status');
		$data['entry_order_failure_status'] = $this->language->get('entry_order_failure_status');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['type'])) {
			$data['error_type'] = $this->error['type'];
		} else {
			$data['error_type'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/sberbank', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/sberbank', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

		if (isset($this->request->post['sberbank_username'])) {
			$data['sberbank_username'] = $this->request->post['sberbank_username'];
		} else {
			$data['sberbank_username'] = $this->config->get('sberbank_username');
		}

		if (isset($this->request->post['sberbank_password'])) {
			$data['sberbank_password'] = $this->request->post['sberbank_password'];
		} else {
			$data['sberbank_password'] = $this->config->get('sberbank_password');
		}

		if (isset($this->request->post['sberbank_mode'])) {
			$data['sberbank_mode'] = $this->request->post['sberbank_mode'];
		} else {
			$data['sberbank_mode'] = $this->config->get('sberbank_mode');
		}

		if (isset($this->request->post['sberbank_order_status_id'])) {
			$data['sberbank_order_status_id'] = $this->request->post['sberbank_order_status_id'];
		} else {
			$data['sberbank_order_status_id'] = $this->config->get('sberbank_order_status_id');
		}

		if (isset($this->request->post['sberbank_order_success_status_id'])) {
			$data['sberbank_order_success_status_id'] = $this->request->post['sberbank_order_success_status_id'];
		} else {
			$data['sberbank_order_success_status_id'] = $this->config->get('sberbank_order_success_status_id');
		}

		if (isset($this->request->post['sberbank_order_reversed_status_id'])) {
			$data['sberbank_order_reversed_status_id'] = $this->request->post['sberbank_order_reversed_status_id'];
		} else {
			$data['sberbank_order_reversed_status_id'] = $this->config->get('sberbank_order_reversed_status_id');
		}

		if (isset($this->request->post['sberbank_order_processing_status_id'])) {
			$data['sberbank_order_processing_status_id'] = $this->request->post['sberbank_order_processing_status_id'];
		} else {
			$data['sberbank_order_processing_status_id'] = $this->config->get('sberbank_order_processing_status_id');
		}

		if (isset($this->request->post['sberbank_order_failure_status_id'])) {
			$data['sberbank_order_failure_status_id'] = $this->request->post['sberbank_order_failure_status_id'];
		} else {
			$data['sberbank_order_failure_status_id'] = $this->config->get('sberbank_order_failure_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['sberbank_status'])) {
			$data['sberbank_status'] = $this->request->post['sberbank_status'];
		} else {
			$data['sberbank_status'] = $this->config->get('sberbank_status');
		}

		if (isset($this->request->post['sberbank_sort_order'])) {
			$data['sberbank_sort_order'] = $this->request->post['sberbank_sort_order'];
		} else {
			$data['sberbank_sort_order'] = $this->config->get('sberbank_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/sberbank', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/sberbank')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['sberbank_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['sberbank_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		return !$this->error;
	}
}