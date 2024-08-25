<?php
class ControllerExtensionTotalCustomizableDiscount extends Controller {
    private $error = array();

    public function index() {
        $this->load->model('extension/total/customizable_discount');
        $this->load->model('localisation/language');
        $this->load->language('extension/total/customizable_discount');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('total_customizable_discount', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['module'])) {
            $data['error_module'] = $this->error['module'];
        } else {
            $data['error_module'] = array();
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/total/customizable_discount', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/total/customizable_discount', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['total_customizable_discount_status'])) {
            $data['total_customizable_discount_status'] = $this->request->post['total_customizable_discount_status'];
        } else {
            $data['total_customizable_discount_status'] = $this->config->get('total_customizable_discount_status');
        }

        if (isset($this->request->post['total_customizable_discount_association'])) {
            $data['total_customizable_discount_association'] = $this->request->post['total_customizable_discount_association'];
        } else {
            $data['total_customizable_discount_association'] = $this->config->get('total_customizable_discount_association');
        }

        if (isset($this->request->post['total_customizable_discount_sort_order'])) {
            $data['total_customizable_discount_sort_order'] = $this->request->post['total_customizable_discount_sort_order'];
        } else {
            $data['total_customizable_discount_sort_order'] = $this->config->get('total_customizable_discount_sort_order');
        }

        if (isset($this->request->post['total_customizable_discount_modules'])) {
            $data['total_customizable_discount_modules'] = $this->request->post['total_customizable_discount_modules'];
        } elseif (is_array($this->config->get('total_customizable_discount_modules'))) {
            $data['total_customizable_discount_modules'] = $this->config->get('total_customizable_discount_modules');
        } else {
            $data['total_customizable_discount_modules'] = array();
        }

        $data['conditions'] = array();
		
		// todo: need to add manufacturer, payment, shipping to conditions array

        $categories = $this->model_extension_total_customizable_discount->getCategories();

        if ($categories) {
            $data['conditions']['categories'] = array(
                'title'  => $this->language->get('text_categories'),
                'values' => array()
            );

            foreach ($categories as $category) {
                $data['conditions']['categories']['values'][ $category['category_id'] ] = array(
                    'value' => 'category_' . $category['category_id'],
                    'name'  => $category['name']
                );
            }
        }

        $this->load->language('extension/total/customizable_discount');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/total/customizable_discount', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/total/customizable_discount')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!empty($this->request->post['total_customizable_discount_modules'])) {
            $i = 0;
            foreach ($this->request->post['total_customizable_discount_modules'] as $module) {
                if (empty($module['name'])) {
                    $this->error['module'][$i]['name'] = $this->language->get('error_name');
                }

                foreach ($module['description'] as $language_id => $description) {
                    if (empty($description['title'])) {
                        $this->error['module'][$i]['description'][$language_id]['title'] = $this->language->get('error_title');
                    }
                }

                if (empty($module['discount'])) {
                    $this->error['module'][$i]['discount'] = $this->language->get('error_discount');
                }

                if (empty($module['conditions'])) {
                    $this->error['module'][$i]['conditions'] = $this->language->get('error_conditions');
                }

                $i++;
            }
        }

        if ($this->error && empty($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }
}