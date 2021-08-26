<?php
class ControllerExtensionModulePersonalTrainerForm extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/personal_trainer');

        $this->document->setTitle($this->language->get('heading_title_form'));

        $this->load->model('extension/module/personal_trainer');

        $this->load->model('tool/image');

        $banners = $this->model_extension_module_personal_trainer->getFormBanner();

        $data['banner_images'] = array();

        foreach($banners as $banner) {
            if (!empty($banner['image'])) {
                list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $banner['image']);
                $banner['image'] = $this->model_tool_image->resize($banner['image'], $width_orig, $height_orig);
            }

            $data['banner_images'][] = array(
                'title' => $banner['title'],
                'image' => $banner['image']
            );
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_extension_module_personal_trainer->addPersonalTrainer($this->request->post);

            $this->session->data['success'] = 'Success';

            $this->response->redirect($this->url->link('extension/module/personal_trainer_form/success'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', true)
        );

        if (isset($this->error['firstname'])) {
            $data['error_firstname'] = $this->error['firstname'];
        } else {
            $data['error_firstname'] = '';
        }

        if (isset($this->error['lastname'])) {
            $data['error_lastname'] = $this->error['lastname'];
        } else {
            $data['error_lastname'] = '';
        }

        if (isset($this->error['telephone'])) {
            $data['error_telephone'] = $this->error['telephone'];
        } else {
            $data['error_telephone'] = '';
        }

        if (isset($this->error['profession'])) {
            $data['error_profession'] = $this->error['profession'];
        } else {
            $data['error_profession'] = '';
        }

        if (isset($this->error['social'])) {
            $data['error_social'] = $this->error['social'];
        } else {
            $data['error_social'] = '';
        }

        $data['action'] = $this->url->link('extension/module/personal_trainer_form');

        if (isset($this->request->post['firstname'])) {
            $data['firstname'] = $this->request->post['firstname'];
        } else {
            $data['firstname'] = '';
        }

        if (isset($this->request->post['lastname'])) {
            $data['lastname'] = $this->request->post['lastname'];
        } else {
            $data['lastname'] = '';
        }

        if (isset($this->request->post['telephone'])) {
            $data['telephone'] = $this->request->post['telephone'];
        } else {
            $data['telephone'] = '';
        }

        if (isset($this->request->post['profession'])) {
            $data['profession'] = $this->request->post['profession'];
        } else {
            $data['profession'] = '';
        }

        if (isset($this->request->post['social'])) {
            $data['social'] = $this->request->post['social'];
        } else {
            $data['social'] = '';
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        // Captcha
        if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('return', (array)$this->config->get('config_captcha_page'))) {
            $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
        } else {
            $data['captcha'] = '';
        }

        if ($this->config->get('config_account_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

            if ($information_info) {
                $data['text_register_agree'] = sprintf($this->language->get('text_register_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title'], $information_info['title']);
            } else {
                $data['text_register_agree'] = '';
            }
        } else {
            $data['text_register_agree'] = '';
        }

        if (isset($this->request->post['agree'])) {
            $data['agree'] = $this->request->post['agree'];
        } else {
            $data['agree'] = false;
        }

        $data['back'] = $this->url->link('extension/module/personal_trainer');

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/module/personal_trainer/personal_trainer_form', $data));
    }

    protected function validate() {
        if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        if ((utf8_strlen($this->request->post['profession']) < 1) || (utf8_strlen($this->request->post['profession']) > 32)) {
            $this->error['profession'] = $this->language->get('error_profession');
        }

        if ((utf8_strlen($this->request->post['social']) < 1) || (utf8_strlen($this->request->post['social']) > 32)) {
            $this->error['social'] = $this->language->get('error_social');
        }

        if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('return', (array)$this->config->get('config_captcha_page'))) {
            $captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

            if ($captcha) {
                $this->error['captcha'] = $captcha;
            }
        }

        if ($this->config->get('config_account_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

            if ($information_info && !isset($this->request->post['agree'])) {
                $this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
            }
        }

        return !$this->error;
    }

    public function success() {
        $this->load->language('extension/module/personal_trainer');

        $this->document->setTitle($this->language->get('heading_title'));

        if(!isset($this->session->data['success'])) {
            $this->response->redirect($this->url->link('common/home'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('account/return', '', true)
        );

        $data['continue'] = $this->url->link('common/home');
        $data['home'] = $this->url->link('common/home');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/module/personal_trainer/personal_trainer_success', $data));
    }
}