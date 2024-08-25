<?php
class ModelExtensionTotalCustomizableDiscount extends Model {
    private $categories = array();
    private $manufacturers = array();
    
    public function getTotal($total) {
        $title = '';
        $modules = $this->config->get('total_customizable_discount_modules');
        $discount = 0;
        $association = $this->config->get('total_customizable_discount_association');
        
        if ($modules) {
            usort($modules, array("ModelExtensionTotalCustomizableDiscount", "modules_sort"));
            
            $products = $this->cart->getProducts();
            $sub_total = $this->getSubTotal($products);
            $used_products = array();
            
            foreach ($products as $key => $product) {
                $products[$key]['categories'] = $this->getCategories($product['product_id']);
                $products[$key]['manufacturer_id'] = $this->getManufacturerId($product['product_id']);
            }
            
            if ($sub_total) {
                $i = 0;
                foreach ($modules as $module) {
                    $this_total = 0;
                    $conditions = isset($module['conditions']) ? $module['conditions'] : array();
                    $is_products = false;
                    $language_id = (int)$this->config->get('config_language_id');
                    $module_title = !empty($module['description'][$language_id]['title']) ? $module['description'][$language_id]['title'] : '';
                    $this_discount = 0;
                    $discount_type = (int)$module['discount_type'];
                    $type_discount = (int)$module['type_discount'];
                    $condition_type = (int)$module['condition'];
                    $this_conditions = array();
                    $module_discount = (float)$module['discount'];
                    $product_quantity = (int)$module['product_quantity'];

                    foreach ($conditions as $condition) {
                        $value = explode('_', $condition['value']);
                        $type = array_shift($value);
                        
                        $value = implode('_', $value);
                        
                        if ($type == 'payment') {
                            if (isset($this->session->data['payment_method']['code']) && $this->session->data['payment_method']['code'] == $value) {
                                $this_conditions[] = 0;
                            }
                        } elseif ($type == 'shipping') {
                            if (isset($this->session->data['shipping_method']['code'])) {
                                $shipping_code = explode('.', $this->session->data['shipping_method']['code']);
                                
                                if ($shipping_code[0] == $value) {
                                    $this_conditions[] = 0;
                                }
                            }
                        } else {
                            $is_products = true;
                            foreach ($products as $key => $product) {
                                $used_product_quantity = isset($used_products[$key]) ? $used_products[$key] : 0;
                                if ($used_product_quantity < $product['quantity']) {
                                    $is = false;
                                    if (($type == 'manufacturer' && $product['manufacturer_id'] == $value) || ($type == 'category' && in_array($value, $product['categories']))) {
                                        $is = true;
                                    }
                                    
                                    if ($is) {
                                        $used_quantity = 1;
                                        
                                        if (!$product_quantity) {
                                            $used_quantity = $product['quantity'];
                                            
                                            if ($used_product_quantity) {
                                                $used_quantity -= $used_product_quantity;
                                            }
                                        }

                                        if ($type_discount) {
                                            $this_conditions[ 'module_' . $i ] = $product['price'] * $used_quantity;
                                        } else {
                                            $this_conditions[] = $product['price'] * $used_quantity;
                                        }
                                        
                                        $used_products[$key] = $used_product_quantity + $used_quantity;
                                        
                                        break;
                                    }
                                }
                            }
                        }

                        if ($condition_type && $this_conditions) {
                            break;
                        }
                    }

                    if (!$is_products) {
                        $this_total = $sub_total;
                    } else {
                        foreach ($this_conditions as $price) {
                            $this_total += $price;
                        }
                    }
                    
                    if ((($type_discount && $this_conditions) || ($condition_type && $this_conditions) || (!$condition_type && count($conditions) == count($this_conditions))) && $this_total) {
                        if ($title) {
                            $title .= '<br>';
                        }
                        $title .= $module_title;
                        
                        if ($discount_type) {
                            $discount += (($module_discount*$this_total)/100);
                        } else {
                            $discount += $module_discount;
                        }
                        
                        if (!$association) {
                            break;
                        }
                    }

                    $i++;
                }
            }
        }
        
        if ($discount > 0) {
            $total['totals'][] = array(
                'code'       => 'customizable_discount',
                'title'      => $title,
                'value'      => -$discount,
                'sort_order' => $this->config->get('total_customizable_discount_sort_order')
            );

            $total['total'] -= $discount;
        }
    }
    
    private function getCategories($product_id) {
        $categories = array();
        
        if (isset($this->categories[$product_id])) {
            $categories = $this->categories[$product_id];
        } else {
            $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
            
            foreach ($query->rows as $category) {
                $categories[] = $category['category_id'];
            }
            
            $this->categories[$product_id] = $categories;
        }
        
        return $categories;
    }    
    private function getManufacturerId($product_id) {
        $manufacturer_id = 0;
        
        if (isset($this->manufacturers[$product_id])) {
            $manufacturer_id = $this->manufacturers[$product_id];
        } else {
            $query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
            
            if (isset($query->row['manufacturer_id'])) {
                $manufacturer_id = $query->row['manufacturer_id'];
            }
            
            $this->manufacturers[$product_id] = $manufacturer_id;
        }
        
        return $manufacturer_id;
    }
    
    protected function modules_sort($a, $b){
        if((int)$a['priority'] == (int)$b['priority']) return 0;
        return ((int)$a['priority'] > (int)$b['priority']) ? -1 : 1;
    }
    
    protected function getSubTotal($products) {
        $total = 0;

        foreach ($products as $product) {
            $total += $product['total'];
        }

        return $total;
    }
}