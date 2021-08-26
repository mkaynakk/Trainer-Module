<?php
class ModelExtensionModulePersonalTrainer extends Model {
	public function addPersonalTrainer($data) {
	    $query = "INSERT INTO ". DB_PREFIX ."personal_trainers (first_name, last_name, image, title, description, instagram_url, education, coupon, content, meta_title, meta_description, meta_keyword)
	    VALUES ('".$this->db->escape($data['first_name'])."', '".$this->db->escape($data['last_name'])."', '".$this->db->escape($data['image'])."', '".$this->db->escape($data['title'])."', '".$this->db->escape($data['description'])."', '".$this->db->escape($data['instagram_url'])."', '".$this->db->escape($data['education'])."', '".$this->db->escape($data['coupon'])."', '".$this->db->escape($data['content'])."', '" .$this->db->escape($data['meta_title']). "', '" .$this->db->escape($data['meta_description']). "', '" .$this->db->escape($data['meta_keyword']). "');";
        $this->db->query($query);

	    $trainer_id = $this->db->getLastId();

	    foreach ($data['product_image'] as $image) {
            $image['media_type'] = 'image';
            $image['media_url'] = $image['image'];
            $image['media_text'] = '';
            $image['media_tag'] = '';
	        $this->addPersonalTrainerMedia($trainer_id, $image);
        }

        foreach ($data['trainer_video'] as $video) {
            $video['media_type'] = 'video';
            $video['media_url'] = $video['video'];
            $video['media_text'] = $video['video_text'];
            $video['media_tag'] = $video['video_tag'];
            $this->addPersonalTrainerMedia($trainer_id, $video);
        }

        // SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'personal_trainer_id=" . (int)$trainer_id . "'");

        if (isset($data['trainer_seo_url'])) {
            foreach ($data['trainer_seo_url']as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'personal_trainer_id=" . (int)$trainer_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    }
                    else {
                        $this->load->helper('custom');
                        $keyword = slug($this->db->escape($data['first_name'])) . slug($this->db->escape($data['last_name']));
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'personal_trainer_id=" . (int)$trainer_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    }
                }
            }
        }

		return $trainer_id;
	}

	public function editPersonalTrainer($personal_trainer_id, $data) {
	    $query = "UPDATE ".DB_PREFIX."personal_trainers t
            SET t.first_name    = '".$this->db->escape($data['first_name'])."',
                t.last_name     = '".$this->db->escape($data['last_name'])."',
                t.image         = '".$this->db->escape($data['image'])."',
                t.title         = '".$this->db->escape($data['title'])."',
                t.description   = '".$this->db->escape($data['description'])."',
                t.instagram_url = '".$this->db->escape($data['instagram_url'])."',
                t.education     = '".$this->db->escape($data['education'])."',
                t.coupon        = '".$this->db->escape($data['coupon'])."',
                t.content       = '".$this->db->escape($data['content'])."',
                t.meta_title    = '".$this->db->escape($data['meta_title'])."', 
                t.meta_description =  '".$this->db->escape($data['meta_description'])."', 
                t.meta_keyword  = '".$this->db->escape($data['meta_keyword'])."'
            WHERE t.personal_trainer_id = ".(int)$personal_trainer_id.";";

	    $this->db->query($query);

        $this->db->query("DELETE FROM " . DB_PREFIX . "personal_trainer_media WHERE personal_trainer_id = '" . (int)$personal_trainer_id . "'");

        if (isset($data['product_image'])) {
            foreach ($data['product_image'] as $image) {
                $image['media_type'] = 'image';
                $image['media_url'] = $image['image'];
                $image['media_text'] = '';
                $image['media_tag'] = '';
                $this->addPersonalTrainerMedia($personal_trainer_id, $image);
            }
        }

        if (isset($data['trainer_video'])) {
            foreach ($data['trainer_video'] as $video) {
                $video['media_type'] = 'video';
                $video['media_url'] = $video['video'];
                $video['media_text'] = $video['video_text'];
                $video['media_tag'] = $video['video_tag'];
                $this->addPersonalTrainerMedia($personal_trainer_id, $video);
            }
        }

        // SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'personal_trainer_id=" . (int)$personal_trainer_id . "'");

        if (isset($data['trainer_seo_url'])) {
            foreach ($data['trainer_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'personal_trainer_id=" . (int)$personal_trainer_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    }
                }
            }
        }
	}

	public function deletePersonalTrainer($personal_trainer_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "personal_trainer_media WHERE personal_trainer_id = '" . (int)$personal_trainer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "personal_trainers WHERE personal_trainer_id = '" . (int)$personal_trainer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'personal_trainer_id=" . (int)$personal_trainer_id . "'");
	}

	public function getPersonalTrainer($personal_trainer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "personal_trainers WHERE personal_trainer_id = " . (int)$personal_trainer_id);
		return $query->row;
	}

    public function getPersonalTrainerSeoUrls($personal_trainer_id) {
        $personal_trainer_seo_url_data = array();

        $query = $this->db->query("SELECT *, (SELECT first_name FROM " . DB_PREFIX . "personal_trainers WHERE personal_trainer_id = '" . (int)$personal_trainer_id . "') AS first_name, (SELECT last_name FROM " . DB_PREFIX . "personal_trainers WHERE personal_trainer_id = '" . (int)$personal_trainer_id . "') AS last_name FROM " . DB_PREFIX . "seo_url WHERE query = 'personal_trainer_id=" . (int)$personal_trainer_id . "'");

        $this->load->helper('custom');

        foreach ($query->rows as $result) {
            $personal_trainer_seo_url_data[$result['store_id']][$result['language_id']] = array(
                'keyword'    => $result['keyword'],
                'first_name' => slug($result['first_name']) . slug($result['last_name'])
            );
        }

        return $personal_trainer_seo_url_data;
    }

	public function getPersonalTrainers($data = array()) {
		$sql = "SELECT *, CONCAT(first_name, ' ', last_name) AS name FROM " . DB_PREFIX . "personal_trainers c";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.first_name, ' ', c.last_name) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

    public function getTotalPersonalTrainers($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "personal_trainers";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

	public function addPersonalTrainerMedia($personal_trainer_id, $data = array()) {
	    $sort_order = isset($data['sort_order']) ? $data['sort_order'] : 0;

	    $query = "INSERT INTO ".DB_PREFIX."personal_trainer_media (personal_trainer_id, media_url, media_text, media_tag, media_type, sort_order)
        VALUES (".(int)$personal_trainer_id.", '".$this->db->escape($data['media_url'])."', '".$this->db->escape($data['media_text'])."', '".$this->db->escape($data['media_tag'])."', '".$this->db->escape($data['media_type'])."', '".$sort_order."');";

        $this->db->query($query);

        return $this->db->getLastId();
    }

    public function getPersonalTrainerMedias($personal_trainer_id, $type = 'all') {
	    $con = $type == 'all' ? '' : " AND media_type = '".$type."'";
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."personal_trainer_media WHERE personal_trainer_id = ".(int)$personal_trainer_id.$con);
        return $query->rows;
    }

    public function deletePersonalTrainerMedia($media_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "personal_trainer_media WHERE personal_trainer_media_id = '" . (int)$media_id . "'");
    }

    public function getPersonalTrainerForms($data = array()) {
        $sql = "SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM " . DB_PREFIX . "personal_trainer_form pf";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(pf.firstname, ' ', pf.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $sort_data = array(
            'name',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY date_added DESC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function deletePersonalTrainerForm($personal_trainer_form_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "personal_trainer_form WHERE form_id = '" . (int)$personal_trainer_form_id . "'");
    }

    public function getTotalPersonalTrainerForms($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "personal_trainer_form";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}
