<?php
class ControllerExtensionModulePersonalTrainerInfo extends Controller {
    public function index()
    {
        $this->load->language('extension/module/personal_trainer');

        $this->load->model('extension/module/personal_trainer');

        $personal_trainer_id = $this->request->get['personal_trainer_id'];

        $results = $this->model_extension_module_personal_trainer->getPersonalTrainer($personal_trainer_id);

        $data['personal_trainer'] = array();

        $count = 0;

        foreach ($results as $result) {

          $data['breadcrumbs'][] = array(
            'text' => $result['first_name'] . ' ' . $result['last_name']
          );

            $this->load->model('tool/image');

            if (!empty($result['image'])) {
                list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $result['image']);
                $result['image'] = $this->model_tool_image->resize($result['image'], $width_orig, $height_orig);
            }

            $data['personal_trainer_media'] = array();

            foreach ($this->model_extension_module_personal_trainer->getPersonalTrainerMedias($personal_trainer_id) as $personal_trainer_media) {

                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $personal_trainer_media['media_url'], $image_thumb);

                $temp = array(
                    'personal_trainer_id' => $personal_trainer_media['personal_trainer_id'],
                    'media_type' => $personal_trainer_media['media_type'],
                    'media_text' => $personal_trainer_media['media_text'],
                    'media_thumb' => !empty($image_thumb) ? $image_thumb[1] : '',
                    'sort_order' => $personal_trainer_media['sort_order']
                );

                if ($personal_trainer_media['media_type'] == 'image') {
                    list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $personal_trainer_media['media_url']);
                    $temp['media_url'] = $this->model_tool_image->resize($personal_trainer_media['media_url'], $width_orig, $height_orig);
                } else {
                    $temp['media_url'] = $personal_trainer_media['media_url'];
                }

                if ($personal_trainer_media['media_type'] == 'video') {
                    $count = $count + 1;
                }

                $data['personal_trainer_media'][] = $temp;
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
            );
        }

        $data['check_data'] = $count;

        $data['trainer_link'] = $this->url->link('product/category', 'path=149');

        $data['home'] = $this->load->controller('common/home');

        foreach ($results as $meta) {
            $this->document->setTitle(!empty($meta['meta_title']) ? $meta['meta_title'] : mb_convert_case($meta['first_name'] . ' ' . $meta['last_name'], MB_CASE_TITLE, 'UTF-8'));
            $this->document->setDescription($meta['meta_description']);
            $this->document->setKeywords($meta['meta_keyword']);
        }

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $data['trainers'] = $this->url->link('extension/module/personal_trainer');

        $this->response->setOutput($this->load->view('extension/module/personal_trainer/personal_trainer_info', $data));
    }
}
