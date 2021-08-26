<?php
class ModelExtensionModulePersonalTrainer extends Model
{

    public function getPersonalTrainers($data = array())
    {
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

    public function getPersonalTrainerMedias($personal_trainer_id) {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."personal_trainer_media WHERE personal_trainer_id = ".(int)$personal_trainer_id." ORDER BY sort_order ASC;");
        return $query->rows;
    }

    public function getPersonalTrainerMediaAll() {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."personal_trainer_media ORDER BY sort_order ASC;");
        return $query->rows;
    }

    public function getPersonalTrainer($personal_trainer_id) {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."personal_trainers WHERE personal_trainer_id = ".(int)$personal_trainer_id.";");
        return $query->rows;
    }

    public function addPersonalTrainer($data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "personal_trainer_form` SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', profession = '" . $this->db->escape($data['profession']) . "', social = '" . $this->db->escape($data['social']). "', date_added = NOW()");

        return $this->db->getLastId();
    }

    public function getExerciseBanner() {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."banner_image WHERE banner_id = 17;");
        return $query->rows;
    }

    public function getFormBanner() {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."banner_image WHERE banner_id = 18;");
        return $query->rows;
    }

    public function getHomeVideos() {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."personal_trainer_media WHERE media_type = 'video' AND sort_order = -1;");
        return $query->rows;
    }
}