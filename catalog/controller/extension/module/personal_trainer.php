<?php
class ControllerExtensionModulePersonalTrainer extends Controller {
    public function index()
    {
        $this->load->language('extension/module/personal_trainer');

        $this->load->model('extension/module/personal_trainer');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $data['trainer_register'] = $this->url->link('extension/module/personal_trainer_form');

            $results = $this->model_extension_module_personal_trainer->getPersonalTrainers();

            $data['personal_trainer'] = array();

            foreach ($results as $result) {

                $personal_trainer_id = $result['personal_trainer_id'];

                $this->load->model('tool/image');

                if (!empty($result['image'])) {
                    list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $result['image']);
                    $result['image'] = $this->model_tool_image->resize($result['image'], $width_orig, $height_orig);
                }

                $data['personal_trainer_media'] = array();

                foreach ($this->model_extension_module_personal_trainer->getPersonalTrainerMedias($personal_trainer_id) as $personal_trainer_media) {

                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $personal_trainer_media['media_url'], $image_thumb);

                    $data['personal_trainer_media'][] = array(
                        'personal_trainer_id' => $personal_trainer_media['personal_trainer_id'],
                        'media_url' => $personal_trainer_media['media_url'],
                        'media_text' => $personal_trainer_media['media_text'],
                        'media_thumb' => !empty($image_thumb) ? $image_thumb[1] : '',
                        'media_type' => $personal_trainer_media['media_type'],
                        'sort_order' => $personal_trainer_media['sort_order']
                    );
                }

                $data['personal_trainer'][] = array(
                    'personal_trainer_id' => $result['personal_trainer_id'],
                    'first_name' => $result['first_name'],
                    'last_name' => $result['last_name'],
                    'image' => $result['image'],
                    'title' => $result['title'],
                    'description' => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                    'instagram_url' => $result['instagram_url'],
                    'education' => $result['education'],
                    'coupon' => $result['coupon'],
                    'content' => html_entity_decode($result['content'], ENT_QUOTES, 'UTF-8'),
                    'personal_trainer_media' => $data['personal_trainer_media'],
                    'href' => $this->url->link('extension/module/personal_trainer_info', 'personal_trainer_id=' . $result['personal_trainer_id'])
                );
            }

            $this->response->setOutput($this->load->view('extension/module/personal_trainer/personal_trainer_list', $data));
        }
}

