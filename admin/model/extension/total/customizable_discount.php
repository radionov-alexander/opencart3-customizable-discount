<?php
class ModelExtensionTotalCustomizableDiscount extends Model {
    public function getCategories($data = array()) {
        $query = $this->db->query("SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'  GROUP BY cp.category_id  ORDER BY name ASC");

        return $query->rows;
    }
    
    public function getManufacturers() {
        $query = $this->db->query("SELECT m.manufacturer_id, md.name FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) WHERE md.language_id = '" . (int)$this->config->get('config_language_id') . "'  ORDER BY name ASC");

        return $query->rows;
    }

    public function getExtensions($type = '') {
        $sql = "SELECT e.code FROM " . DB_PREFIX . "extension e ";

        if ($type) {
            $sql .= "WHERE e.type = '" . $this->db->escape($type) . "' ";
        }

        $sql .= "ORDER BY e.code ASC";

        $query = $this->db->query($sql);

        return $query->rows;
    }
}