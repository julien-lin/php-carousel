<?php
/**
 * PrestaShop Module: PHP Carousel
 * 
 * @author    Julien Linard
 * @copyright 2025
 * @license   MIT
 * @version   1.0.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

// Check if PHP Carousel library is available
if (!class_exists('JulienLinard\Carousel\Carousel')) {
    // Try to autoload if composer is used
    if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
        require_once __DIR__ . '/../../vendor/autoload.php';
    } else {
        return;
    }
}

use JulienLinard\Carousel\Carousel;

class PHPCarousel extends Module
{
    public function __construct()
    {
        $this->name = 'phpcarousel';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Julien Linard';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PHP Carousel');
        $this->description = $this->l('Display beautiful carousels in your PrestaShop store');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install(): bool
    {
        return parent::install() &&
            $this->registerHook('displayHome') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayProductAdditionalInfo');
    }

    public function uninstall(): bool
    {
        return parent::uninstall();
    }

    /**
     * Hook: displayHome
     * Display carousel on homepage
     */
    public function hookDisplayHome($params): string
    {
        try {
            // Get featured products
            $products = $this->getFeaturedProducts();
            
            if (empty($products)) {
                return '';
            }

            // Convert products to carousel items
            $items = [];
            foreach ($products as $product) {
                $items[] = [
                    'id' => (string) $product['id_product'],
                    'title' => $product['name'],
                    'content' => $product['description_short'] ?? '',
                    'image' => $this->context->link->getImageLink(
                        $product['link_rewrite'],
                        $product['id_image'],
                        'home_default'
                    ),
                    'link' => $this->context->link->getProductLink($product['id_product']),
                ];
            }

            $carousel = Carousel::productShowcase('home-products', $items, [
                'itemsPerSlide' => 4,
                'itemsPerSlideMobile' => 1,
                'autoplay' => true,
                'autoplayInterval' => 5000,
            ]);

            return $carousel->render();
        } catch (\Exception $e) {
            if ($this->context->controller->controller_type === 'admin') {
                return '<p class="error">' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            return '';
        }
    }

    /**
     * Hook: displayHeader
     * Add CSS/JS to header if needed
     */
    public function hookDisplayHeader($params): void
    {
        // CSS and JS are included in render() method
        // This hook can be used for additional assets if needed
    }

    /**
     * Hook: displayProductAdditionalInfo
     * Display related products carousel
     */
    public function hookDisplayProductAdditionalInfo($params): string
    {
        if (!isset($params['product'])) {
            return '';
        }

        try {
            $product = $params['product'];
            $relatedProducts = $this->getRelatedProducts($product->id);

            if (empty($relatedProducts)) {
                return '';
            }

            $items = [];
            foreach ($relatedProducts as $related) {
                $items[] = [
                    'id' => (string) $related['id_product'],
                    'title' => $related['name'],
                    'image' => $this->context->link->getImageLink(
                        $related['link_rewrite'],
                        $related['id_image'],
                        'medium_default'
                    ),
                    'link' => $this->context->link->getProductLink($related['id_product']),
                ];
            }

            $carousel = Carousel::card('related-products', $items, [
                'itemsPerSlide' => 4,
                'itemsPerSlideMobile' => 2,
            ]);

            return $carousel->render();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Get featured products
     */
    private function getFeaturedProducts(int $limit = 10): array
    {
        $category = new Category((int) Configuration::get('PS_HOME_CATEGORY'));
        $products = $category->getProducts(
            $this->context->language->id,
            1,
            $limit,
            'position'
        );
        
        return $products ?: [];
    }

    /**
     * Get related products
     */
    private function getRelatedProducts(int $productId, int $limit = 8): array
    {
        $product = new Product($productId);
        $categories = $product->getCategories();
        
        if (empty($categories)) {
            return [];
        }

        $category = new Category((int) $categories[0]);
        $products = $category->getProducts(
            $this->context->language->id,
            1,
            $limit + 1,
            'position'
        );

        // Filter out current product
        return array_filter($products ?: [], function($p) use ($productId) {
            return (int) $p['id_product'] !== $productId;
        });
    }

    /**
     * Get module configuration page
     */
    public function getContent(): string
    {
        $output = '';

        if (Tools::isSubmit('submitPHPCarousel')) {
            // Handle form submission
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        return $output . $this->displayForm();
    }

    /**
     * Display configuration form
     */
    private function displayForm(): string
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Carousel ID'),
                    'name' => 'PHPCAROUSEL_ID',
                    'size' => 20,
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submitPHPCarousel';
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
        ];

        $helper->fields_value['PHPCAROUSEL_ID'] = Configuration::get('PHPCAROUSEL_ID');

        return $helper->generateForm($fieldsForm);
    }
}

