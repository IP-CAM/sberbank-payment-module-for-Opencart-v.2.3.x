<?php
class ControllerExtensionPaymentSberBank extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');
		return $this->load->view('extension/payment/sberbank', $data);
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'sberbank') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('sberbank_order_status_id'));

			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
	
			$vars = [];
			$vars['userName'] = $this->config->get('sberbank_username');
			$vars['password'] = $this->config->get('sberbank_password');
			$vars['orderNumber'] = $order_info['order_id'];
			
			// $cart = [
			// 	[
			// 		'positionId' => 1,
			// 		'name' => 'Название товара',
			// 		'quantity' => [
			// 			'value' => 1,    
			// 			'measure' => 'шт'
			// 		],
			// 		'itemAmount' => 1000 * 100,
			// 		'itemCode' => '123456',
			// 		'tax' => [
			// 			'taxType' => 0,
			// 			'taxSum' => 0
			// 		],
			// 		'itemPrice' => 1000 * 100,
			// 	]
			// ];
			
			// $vars['orderBundle'] = json_encode(
			// 	[
			// 		'cartItems' => [
			// 			'items' => $cart
			// 		]
			// 	], 
			// 	JSON_UNESCAPED_UNICODE
			// ];
			
			$vars['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
			
			/* URL куда клиент вернется в случае успешной оплаты */
			$vars['returnUrl'] = $this->url->link('checkout/success', '', true);
				
			/* URL куда клиент вернется в случае ошибки */
			$vars['failUrl'] = $this->url->link('checkout/failure', '', true);
			
			/* Описание заказа, не более 24 символов, запрещены % + \r \n */
			$vars['description'] = 'Заказ №' . $order_info['order_id'] . ' на' . HTTPS_SERVER;
			
			$ch = curl_init('https://3dsec.sberbank.ru/payment/rest/register.do?' . http_build_query($vars));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$response = curl_exec($ch);
			curl_close($ch);
	
			$json = json_decode($response, true);
			if (empty($json['orderId'])){
				$data['error'] = $json['errorMessage'];
			} else {
				$data['url'] = $json['formUrl'];
			}

			$this->response->addHeader('Content-type: application/json');
			$this->response->setOutput(json_encode($data));
		}
	}

	public function callback() {
		if (isset($this->request->get['mdOrder'])) {
			$order_id = $this->request->get['orderNumber'];
			$status = $this->request->get['status'];

			$this->load->model('checkout/order');
			if ($status) {
				$status_id = $this->config->get('sberbank_order_success_status_id');
			} else {
				$status_id = $this->config->get('sberbank_order_failure_status_id');
			}
			$this->model_checkout_order->addOrderHistory($order_id, $status_id);
		}
	}
}