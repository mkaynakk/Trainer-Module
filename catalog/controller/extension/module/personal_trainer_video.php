<?php
class ControllerExtensionModulePersonalTrainerVideo extends Controller {
    public function index() {
        $this->load->language('extension/module/personal_trainer');
        $this->load->model('extension/module/personal_trainer');
        $this->document->setTitle($this->language->get('Eğitmen Videolar'));

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $results = $this->model_extension_module_personal_trainer->getPersonalTrainerMediaAll();

        $personal_trainer_medias = array();

        foreach ($results as $result) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $result['media_url'], $image_thumb);

            $personal_trainer_medias[] = array(
                'personal_trainer_id' => $result['personal_trainer_id'],
                'media_url' => $result['media_url'],
                'media_text' => $result['media_text'],
                'media_tag'  => $result['media_tag'],
                'media_thumb' => !empty($image_thumb) ? $image_thumb[1] : '',
                'media_type' => $result['media_type'],
                'sort_order' => $result['sort_order']
            );
        }

        $tag_category = array();

        foreach($personal_trainer_medias as $tags){
            $tag = $tags['media_tag'];
            if($tag == null){
                continue;
            }
            if(isset($tag_category[$tag])){
                array_push($tag_category[$tag], $tags);
            } else {
                $tag_category[$tag] = [$tags];
            }
        }

        $data['video_tags'] = $tag_category;

        $this->load->model('tool/image');

        $banners = $this->model_extension_module_personal_trainer->getExerciseBanner();

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

        $this->response->setOutput($this->load->view('extension/module/personal_trainer/personal_trainer_video', $data));
    }
}