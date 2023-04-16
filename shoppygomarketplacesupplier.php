<?php
/**
 * Copyright since 2022 Bwlab of Luigi Massa and Contributors
 * Bwlab of Luigi Massa is an Italy Company
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shoppygo.io so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade ShoppyGo to newer
 * versions in the future. If you wish to customize ShoppyGo for your
 * needs please refer to https://docs.shoppygo.io/ for more information.
 *
 * @author    Bwlab and Contributors <contact@shoppygo.io>
 * @copyright Since 2022 Bwlab of Luigi Massa and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use ShoppyGo\MarketplaceBundle\Entity\MarketplaceSeller;

require_once _PS_MODULE_DIR_.'shoppygomarketplacesupplier'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.
    'autoload.php';

class Shoppygomarketplacesupplier extends Module implements WidgetInterface
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'shoppygomarketplacesupplier';
        $this->version = '1.0.0';

        $this->author = 'ShoppyGo';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('ShoppyGo Supplier', [], 'Modules.Shoppygomarketplacesupplier.Admin');
        //TODO: Edit  ''description here'
        $this->description = $this->trans('Extend Supplier Data', [], 'Modules.Shoppygomarketplacesupplier.Admin');
        $this->confirmUninstall = $this->trans('Are you sure?', [], 'Modules.Shoppygomarketplacesupplier.Admin');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet($this->name.'-css-'.$this->version, $this->_path.'views/css/shoppygomarketplacesupplier.css', ['media' => 'all', 'priority' => 150]);
    }

    public function install()
    {
        return parent::install() && $this->registerHook('actionFrontControllerSetMedia');
    }

    public function renderWidget($hookName, array $configuration)
    {
        if ('displaySellerPolicy' === $hookName) {
            $repo = $this->get('doctrine')
                ->getManager()
                ->getRepository(MarketplaceSeller::class)
            ;
            $seller = $repo->findOneBy(['id_seller' => $configuration['id_seller']]);
            $policy = $seller->getReturnPolicy();
            $this->smarty->assign(['policy' => $policy]);

            return $this->display(__FILE__, '/views/templates/hook/displaySellerPolicy.tpl');
        }
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->unregisterHook('actionFrontControllerSetMedia');
    }
}
