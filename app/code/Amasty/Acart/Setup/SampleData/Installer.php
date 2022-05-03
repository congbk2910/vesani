<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Setup\SampleData;

use Amasty\Base\Model\MagentoVersion;
use Magento\Email\Model\Template;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\Setup\SampleData\InstallerInterface;

class Installer implements InstallerInterface
{
    /**
     * @var State
     */
    private $appState;

    /**
     * @var MagentoVersion
     */
    private $magentoVersion;

    public function __construct(
        State $appState,
        MagentoVersion $magentoVersion
    ) {
        $this->appState = $appState;
        $this->magentoVersion = $magentoVersion;
    }

    public function install()
    {
        $this->appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$this, 'createEmailTemplate']
        );
    }

    public function createEmailTemplate()
    {
        $templateCode = 'amasty_acart_template';
        $template = ObjectManager::getInstance()
            ->create(Template::class);
        $template->setForcedArea($templateCode);
        $template->loadDefault($templateCode);
        $template->setData('orig_template_code', $templateCode);
        $template->setData(
            'template_variables',
            \Zend_Json::encode($template->getVariablesOptionArray(true))
        );
        $template->setData('template_code', 'Amasty: Abandoned Cart Reminder');
        $template->setTemplateType(Template::TYPE_HTML);
        $template->setId(null);
        if (version_compare($this->magentoVersion->get(), '2.3.4', '>=')) {
            $template->setIsLegacy(true);
        }
        $template->save();
    }
}
