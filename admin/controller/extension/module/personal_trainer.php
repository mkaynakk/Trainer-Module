<?php

class ControllerExtensionModulePersonalTrainer extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/personal_trainer');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/personal_trainer');
        $this->load->model('setting/setting');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('module_title'),
            'href' => $this->url->link('extension/module/personal_trainer', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['personal_trainer'] = $this->model_setting_setting->getSetting('module_personal_trainer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_setting_setting->editSetting('module_personal_trainer', $this->request->post);
            $this->session->data['success'] = $this->language->get('success_message');

            $event = $this->model_setting_event->getEventByCode('personal_trainer_link');

            if ($this->request->post['module_personal_trainer_status'] == '1') {
                $this->model_setting_event->enableEvent($event['event_id']);
            } else {
                $this->model_setting_event->disableEvent($event['event_id']);
            }

            $this->response->redirect($this->url->link('extension/module/personal_trainer', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $this->response->setOutput($this->load->view('extension/module/personal_trainer', $data));
    }

    public function list() {
        $this->load->language('extension/module/personal_trainer');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/personal_trainer');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/personal_trainer', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['trainers'] = array();

        $filter_data = array(
            'filter_name'              => $filter_name,
            'sort'                     => $sort,
            'order'                    => $order,
            'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                    => $this->config->get('config_limit_admin')
        );

        $trainer_total = $this->model_extension_module_personal_trainer->getTotalPersonalTrainers($filter_data);

        $results = $this->model_extension_module_personal_trainer->getPersonalTrainers($filter_data);

        foreach ($results as $result) {
            $data['trainers'][] = array(
                'personal_trainer_id' => $result['personal_trainer_id'],
                'first_name'    => $result['first_name'],
                'last_name'     => $result['last_name'],
                'title'         => $result['title'],
                'description'   => $result['description'],
                'instagram_irl' => $result['instagram_url'],
                'education'     => $result['education'],
                'coupon'      => $result['coupon'],
                'content'       => $result['content'],
                'edit' => $this->url->link('extension/module/personal_trainer/edit','user_token=' . $this->session->data['user_token'].'&personal_trainer_id='. $result['personal_trainer_id'], true),
                'delete' => $this->url->link('extension/module/personal_trainer/delete','user_token=' . $this->session->data['user_token'].'&personal_trainer_id='. $result['personal_trainer_id'], true)
            );
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('extension/module/personal_trainer', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        $pagination = new Pagination();
        $pagination->total = $trainer_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/personal_trainer/list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($trainer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($trainer_total - $this->config->get('config_limit_admin'))) ? $trainer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $trainer_total, ceil($trainer_total / $this->config->get('config_limit_admin')));

        $data['filter_name'] = $filter_name;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['add_new_link'] = $this->url->link('extension/module/personal_trainer/add', 'user_token=' . $this->session->data['user_token'], true);
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/personal_trainer/list', $data));
    }

    public function add() {
        $this->load->language('extension/module/personal_trainer');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/personal_trainer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_module_personal_trainer->addPersonalTrainer($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/personal_trainer/list', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getForm();
    }

    public function edit() {
        $this->load->language('extension/module/personal_trainer');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/personal_trainer');
        
        if ($this->request->server['REQUEST_METHOD'] == 'POST'){
            if ($this->request->post['trainer_seo_url']) {
                $this->load->model('design/seo_url');

                foreach ($this->request->post['trainer_seo_url'] as $store_id => $language) {
                    foreach ($language as $language_id => $keyword) {
                        if (!empty($keyword) || $this->request->get['route'] == 'extension/module/personal_trainer/add') {
                            if (count(array_keys($language, $keyword)) > 1) {
                                $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
                            }

                            $seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

                            foreach ($seo_urls as $seo_url) {
                                if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['personal_trainer_id']) || (($seo_url['query'] != 'personal_trainer_id=' . $this->request->get['personal_trainer_id'])))) {
                                    $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');

                                    break;
                                }
                            }
                        }
                        else {
                            $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_seo');
                        }
                    }
                }
            }
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_module_personal_trainer->editPersonalTrainer($this->request->get['personal_trainer_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/personal_trainer/list', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm('extension/module/personal_trainer/edit');
    }

    public function delete(){
        $this->load->model('extension/module/personal_trainer');
        $trainer_id = $this->request->get['personal_trainer_id'];
        $delete_trainer = $this->model_extension_module_personal_trainer->deletePersonalTrainer($trainer_id);
        $this->response->redirect($this->url->link('extension/module/personal_trainer/list', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
    }

    protected function getForm($view = 'extension/module/personal_trainer/add') {
        $data['text_form'] = !isset($this->request->get['personal_trainer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->get['personal_trainer_id'])) {
            $data['personal_trainer_id'] = $this->request->get['personal_trainer_id'];
        } else {
            $data['personal_trainer_id'] = 0;
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['first_name'])) {
            $data['error_first_name'] = $this->error['first_name'];
        } else {
            $data['error_first_name'] = '';
        }

        if (isset($this->error['last_name'])) {
            $data['error_last_name'] = $this->error['last_name'];
        } else {
            $data['error_last_name'] = '';
        }

        if (isset($this->error['title'])) {
            $data['error_title'] = $this->error['title'];
        } else {
            $data['error_title'] = '';
        }

        if (isset($this->error['keyword'])) {
            $data['error_keyword'] = $this->error['keyword'];
        } else {
            $data['error_keyword'] = '';
        }

        $url = '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['personal_trainer_id'])) {
            $data['action'] = $this->url->link('extension/module/personal_trainer/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/personal_trainer/edit', 'user_token=' . $this->session->data['user_token'] . '&personal_trainer_id=' . $this->request->get['personal_trainer_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('extension/module/personal_trainer/list', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['base_url'] = HTTP_CATALOG;

        $data['media'] = [];

        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        if (isset($this->request->get['personal_trainer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $trainer_info = $this->model_extension_module_personal_trainer->getPersonalTrainer($this->request->get['personal_trainer_id']);
            $data['images'] = $this->model_extension_module_personal_trainer->getPersonalTrainerMedias($this->request->get['personal_trainer_id'], 'image');
            $data['videos'] = $this->model_extension_module_personal_trainer->getPersonalTrainerMedias($this->request->get['personal_trainer_id'], 'video');
        }

        if (isset($this->request->post['first_name'])) {
            $data['first_name'] = $this->request->post['first_name'];
        } elseif (!empty($trainer_info)) {
            $data['first_name'] = $trainer_info['first_name'];
        } else {
            $data['first_name'] = '';
        }

        if (isset($this->request->post['last_name'])) {
            $data['last_name'] = $this->request->post['last_name'];
        } elseif (!empty($trainer_info)) {
            $data['last_name'] = $trainer_info['last_name'];
        } else {
            $data['last_name'] = '';
        }

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($trainer_info)) {
            $data['image'] = $trainer_info['image'];
        } else {
            $data['image'] = '';
        }

        if (isset($this->request->post['title'])) {
            $data['title'] = $this->request->post['title'];
        } elseif (!empty($trainer_info)) {
            $data['title'] = $trainer_info['title'];
        } else {
            $data['title'] = '';
        }

        if (isset($this->request->post['description'])) {
            $data['description'] = $this->request->post['description'];
        } elseif (!empty($trainer_info)) {
            $data['description'] = $trainer_info['description'];
        } else {
            $data['description'] = '';
        }

        if (isset($this->request->post['instagram_url'])) {
            $data['instagram_url'] = $this->request->post['instagram_url'];
        } elseif (!empty($trainer_info)) {
            $data['instagram_url'] = $trainer_info['instagram_url'];
        } else {
            $data['instagram_url'] = '';
        }

        if (isset($this->request->post['education'])) {
            $data['education'] = $this->request->post['education'];
        } elseif (!empty($trainer_info)) {
            $data['education'] = $trainer_info['education'];
        } else {
            $data['education'] = '';
        }

        if (isset($this->request->post['coupon'])) {
            $data['coupon'] = $this->request->post['coupon'];
        } elseif (!empty($trainer_info)) {
            $data['coupon'] = $trainer_info['coupon'];
        } else {
            $data['coupon'] = '';
        }

        if (isset($this->request->post['content'])) {
            $data['content'] = $this->request->post['content'];
        } elseif (!empty($trainer_info)) {
            $data['content'] = $trainer_info['content'];
        } else {
            $data['content'] = '';
        }

        if (isset($this->request->post['meta_title'])) {
            $data['meta_title'] = $this->request->post['meta_title'];
        } elseif (!empty($trainer_info)) {
            $data['meta_title'] = $trainer_info['meta_title'];
        } else {
            $data['meta_title'] = '';
        }

        if (isset($this->request->post['meta_description'])) {
            $data['meta_description'] = $this->request->post['meta_description'];
        } elseif (!empty($trainer_info)) {
            $data['meta_description'] = $trainer_info['meta_description'];
        } else {
            $data['meta_description'] = '';
        }

        if (isset($this->request->post['meta_keyword'])) {
            $data['meta_keyword'] = $this->request->post['meta_keyword'];
        } elseif (!empty($trainer_info)) {
            $data['meta_keyword'] = $trainer_info['meta_keyword'];
        } else {
            $data['meta_keyword'] = '';
        }

        if (isset($this->request->post['trainer_seo_url'])) {
            $data['trainer_seo_url'] = $this->request->post['trainer_seo_url'];
        } elseif (isset($this->request->get['personal_trainer_id'])) {
            $data['trainer_seo_url'] = $this->model_extension_module_personal_trainer->getPersonalTrainerSeoUrls($this->request->get['personal_trainer_id']);
        } else {
            $data['trainer_seo_url'] = array();
        }

        $this->load->model('setting/store');

        $data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->language->get('text_default')
        );

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($view, $data));
    }

    protected function validateForm() {
        if ((utf8_strlen($this->request->post['first_name']) < 1) || (utf8_strlen(trim($this->request->post['first_name'])) > 32)) {
            $this->error['first_name'] = $this->language->get('error_first_name');
        }

        if ((utf8_strlen($this->request->post['last_name']) < 1) || (utf8_strlen(trim($this->request->post['last_name'])) > 32)) {
            $this->error['last_name'] = $this->language->get('error_last_name');
        }

        if ((utf8_strlen($this->request->post['title']) < 1) || (utf8_strlen(trim($this->request->post['title'])) > 32)) {
            $this->error['title'] = $this->language->get('error_title');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    public function addSubmenu(&$route = false, &$data = false, &$output = false){

        // $this aktif olan sayfanının this'i olduğu için eğitmenler sayfasının dil dosyası load edildi.
        $this->load->language('extension/module/personal_trainer');

        $menu_index = null;

        foreach (array_values($data['menus']) as $i => $value) {
            if ($value['id'] == 'menu-catalog'){
                $menu_index = $i;
                break;
            }
        }

        if ($menu_index != null){
            $data['menus'][$menu_index]['children'][] = array(
                'name'     => $this->language->get('heading_title'),
                'href'     => $this->url->link('extension/module/personal_trainer/list', 'user_token=' . $this->session->data['user_token'], true),
                'children' => array()
            );
        }

        if ($menu_index != null){
            $data['menus'][$menu_index]['children'][] = array(
                'name'     => $this->language->get('text_form'),
                'href'     => $this->url->link('extension/module/personal_trainer/form', 'user_token=' . $this->session->data['user_token'], true),
                'children' => array()
            );
        }
    }

    public function form() {
        $this->load->language('extension/module/personal_trainer');

        $this->load->model('extension/module/personal_trainer');

        $this->document->setTitle('Eğitmen Başvuru');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('Eğitmen Başvuru'),
            'href' => $this->url->link('extension/module/personal_trainer/form', 'user_token=' . $this->session->data['user_token'], true)
        );

        $filter_data = array(
            'filter_name'              => $filter_name,
            'sort'                     => $sort,
            'order'                    => $order,
            'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                    => $this->config->get('config_limit_admin')
        );

        $trainer_form_total = $this->model_extension_module_personal_trainer->getTotalPersonalTrainerForms($filter_data);

        $personal_trainer_forms = $this->model_extension_module_personal_trainer->getPersonalTrainerForms($filter_data);

        foreach($personal_trainer_forms as $personal_trainer_form) {
            $data['personal_trainer_forms'][] = array(
                'firstname'    => $personal_trainer_form['firstname'],
                'lastname'     => $personal_trainer_form['lastname'],
                'telephone'    => $personal_trainer_form['telephone'],
                'profession'   => $personal_trainer_form['profession'],
                'social'       => $personal_trainer_form['social'],
                'delete'       => $this->url->link('extension/module/personal_trainer/form_delete','user_token=' . $this->session->data['user_token'].'&personal_trainer_form_id='. $personal_trainer_form['form_id'], true),
                'date_added'   => $personal_trainer_form['date_added']
            );
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }


        $data['sort_name'] = $this->url->link('extension/module/personal_trainer/form', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        $pagination = new Pagination();
        $pagination->total = $trainer_form_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/personal_trainer/form', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($trainer_form_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($trainer_form_total - $this->config->get('config_limit_admin'))) ? $trainer_form_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $trainer_form_total, ceil($trainer_form_total / $this->config->get('config_limit_admin')));

        $data['filter_name'] = $filter_name;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/personal_trainer/form', $data));
    }

    public function form_delete(){
        $this->load->model('extension/module/personal_trainer');
        $trainer_form_id = $this->request->get['personal_trainer_form_id'];
        $delete_trainer_form = $this->model_extension_module_personal_trainer->deletePersonalTrainerForm($trainer_form_id);
        $this->response->redirect($this->url->link('extension/module/personal_trainer/form', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
    }

    public function install() {
        // Logs Table Create
        $this->db->query("CREATE TABLE `".DB_PREFIX."personal_trainers`
        (
            personal_trainer_id INT NOT NULL AUTO_INCREMENT,
            `first_name`          VARCHAR(255) NOT NULL,
            `last_name`           VARCHAR(255) NOT NULL,
            `image`         TEXT NULL,
            `title`             VARCHAR(255) NOT NULL,
            `description`         TEXT NULL,
            `instagram_url`       TEXT NULL,
            `education`           VARCHAR(255) NULL,
            `coupon`            VARCHAR(255) NOT NULL,
            `content`             TEXT NULL,
            `meta_title`       VARCHAR(255) NOT NULL,
            `meta_description`       VARCHAR(255) NOT NULL,
            `meta_keyword`       VARCHAR(255) NOT NULL
            PRIMARY KEY (personal_trainer_id)
        ) ENGINE=MyISAM;");

        $this->db->query("CREATE TABLE `".DB_PREFIX."personal_trainer_media`
        (
            `personal_trainer_media_id` INT NOT NULL AUTO_INCREMENT,
            `personal_trainer_id`       INT NOT NULL,
            `media_url`                TEXT NOT NULL,
            `media_text`             VARCHAR(255) NULL,
            `media_tag`             TEXT NULL,
            `media_type`                ENUM ('image', 'video') NOT NULL,
            `sort_order`                INT NULL DEFAULT 0,
             PRIMARY KEY (personal_trainer_media_id)       
        ) ENGINE=MyISAM;");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` 
            SET 
            `store_id` = '0', 
            `code` = '" . $this->db->escape('module_personal_trainer') . "', 
            `key` = '" . $this->db->escape('module_personal_trainer_status') . "', 
            `value` = '0'
            ");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` 
            SET 
            `store_id` = '0', 
            `language_id` = '" . (int)$this->config->get('config_language_id') . "',
            `query` = '" . $this->db->escape('extension/module/personal_trainer') . "', 
            `keyword` = '" . $this->db->escape('egitmenler') . "'
            ");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` 
            SET 
            `store_id` = '0', 
            `language_id` = '" . (int)$this->config->get('config_language_id') . "',
            `query` = '" . $this->db->escape('extension/module/personal_trainer_video') . "', 
            `keyword` = '" . $this->db->escape('egzersizler') . "'
            ");

        $this->load->model('setting/extension');
        $this->model_setting_extension->install('module', 'personal_trainer');

        $this->load->model('setting/event');
        $this->model_setting_event->addEvent('personal_trainer_link', 'admin/view/common/column_left/before', 'extension/module/personal_trainer/addSubmenu', 0);
    }

    public function uninstall(){
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "personal_trainer_media`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "personal_trainers`");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE `query` = 'extension/module/personal_trainer'");

        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('module_personal_trainer');

        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('personal_trainer_link');

        $this->load->model('setting/extension');
        $this->model_setting_extension->uninstall('module', 'personal_trainer');
    }
}