<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Ui\DataProvider\Rule\Modifier;

use Amasty\Acart\Api\ScheduleEmailTemplateRepositoryInterface;
use Amasty\Acart\Model\OptionSource\EmailTemplates;
use Amasty\Acart\Model\OptionSource\SalesRules;
use Amasty\Acart\Model\ResourceModel\Schedule\Collection;
use Amasty\Acart\Model\Rule;
use Amasty\Acart\Model\Schedule as ScheduleModel;
use Amasty\Base\Model\ModuleInfoProvider;
use Magento\Email\Model\Template\Config as TemplateConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Variable\Model\Source\Variables;
use Magento\Variable\Model\VariableFactory;

class Schedule implements ModifierInterface
{
    public const CRON_FAQ = 'https://amasty.com/blog/configure-magento-cron-job?utm_source=extension&utm_medium=tooltip'
    . '&utm_campaign=abandoned-cart-m2-cron-recommended-settings';
    public const QUOTE_LIFETIMES_CONFIG_PATH = 'checkout/cart/delete_quote_after';

    /**
     * @var Collection
     */
    private $currentScheduleCollection;

    /**
     * @var SalesRules
     */
    private $salesRulesOptions;

    /**
     * @var EmailTemplates
     */
    private $emailTemplatesOptions;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TemplateConfig
     */
    private $templateConfig;

    /**
     * @var Variables
     */
    private $variables;

    /**
     * @var VariableFactory
     */
    private $variableFactory;

    /**
     * @var ScheduleEmailTemplateRepositoryInterface
     */
    private $emailTemplateRepository;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    public function __construct(
        Registry $coreRegistry,
        SalesRules $salesRulesOptions,
        EmailTemplates $emailTemplatesOptions,
        ModuleInfoProvider $moduleInfoProvider,
        Manager $moduleManager,
        ScopeConfigInterface $scopeConfig,
        TemplateConfig $templateConfig,
        Variables $variables,
        VariableFactory $variableFactory,
        ScheduleEmailTemplateRepositoryInterface $emailTemplateRepository,
        UrlInterface $urlBuilder,
        JsonSerializer $serializer
    ) {
        $this->currentScheduleCollection = $coreRegistry->registry(Rule::CURRENT_AMASTY_ACART_RULE)
            ->getScheduleCollection();
        $this->salesRulesOptions = $salesRulesOptions;
        $this->emailTemplatesOptions = $emailTemplatesOptions;
        $this->moduleInfoProvider = $moduleInfoProvider;
        $this->moduleManager = $moduleManager;
        $this->scopeConfig = $scopeConfig;
        $this->templateConfig = $templateConfig;
        $this->variables = $variables;
        $this->variableFactory = $variableFactory;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->urlBuilder = $urlBuilder;
        $this->serializer = $serializer;
    }

    public function modifyData(array $data): array
    {
        $data['schedule'] = $this->currentScheduleCollection->getData();
        foreach ($data['schedule'] as &$item) {
            try {
                $item['template_id'] = $item['template_id'] ?: 0;
                $item['custom_template'] = $this->emailTemplateRepository->getByScheduleId(
                    (int)$item[ScheduleModel::SCHEDULE_ID]
                )->getData();
            } catch (NotFoundException $e) {
                $item['custom_template'] = [];
            }
            $item['custom_template']['template_variables'] = $this->serializer->serialize(
                $this->getVariablesOptionArray($item['custom_template']['orig_template_variables'] ?? null)
            );
        }

        return $data;
    }

    public function modifyMeta(array $meta): array
    {
        $scheduleConfigPath = &$meta['schedule_fieldset']['children']['schedule']['arguments']['data']['config'];

        $scheduleConfigPath['salesRuleData'] = $this->salesRulesOptions->toOptionArray();
        $scheduleConfigPath['baseEmailTemplates'] = $this->getEmailTemplatesOptions();
        $scheduleConfigPath['emailTemplates'] = array_merge(
            $this->emailTemplatesOptions->toOptionArray(),
            [['label' => __('Custom Template'), 'value' => 0]]
        );
        $scheduleConfigPath['cronUrl'] = self::CRON_FAQ;
        $scheduleConfigPath['promotionExtensionUrl'] = $this->getPromotionExtensionUrl();
        $scheduleConfigPath['isModuleRulesEnabled'] = $this->moduleManager->isEnabled('Amasty_Rules');
        $scheduleConfigPath['isQuoteLifetimeNoticeAvailable'] = $this->isQuoteLifetimeNoticeAvailable();
        $scheduleConfigPath['variables'] = $this->getVariables();
        $scheduleConfigPath['loadTemplateUrl'] = $this->urlBuilder->getUrl('admin/email_template/defaultTemplate');

        return $meta;
    }

    private function getPromotionExtensionUrl(): string
    {
        return $this->moduleInfoProvider->isOriginMarketplace()
            ? 'https://marketplace.magento.com/amasty-module-special-promotions.html'
            : 'https://amasty.com/special-promotions-pro-for-magento-2.html'
                . '?utm_source=extension&utm_medium=link&utm_campaign=acart-spp-m2';
    }

    private function isQuoteLifetimeNoticeAvailable(): bool
    {
        $quoteLifetimes = $this->scopeConfig->getValue(
            self::QUOTE_LIFETIMES_CONFIG_PATH,
            ScopeInterface::SCOPE_STORE
        );

        if ($this->currentScheduleCollection->getSize() > 0 && $quoteLifetimes) {
            foreach ($this->currentScheduleCollection->getItems() as $schedule) {
                if ($schedule->getDays() >= $quoteLifetimes) {
                    return true;
                }
            }
        }

        return false;
    }

    private function getVariables(): array
    {
        $variables = $this->variables->toOptionArray(true);
        $customVariables = $this->variableFactory->create()->getVariablesOptionArray(true);
        if ($customVariables) {
            $variables = array_merge_recursive($variables, $customVariables);
        }

        return $variables;
    }

    private function getEmailTemplatesOptions(): array
    {
        $options = $this->templateConfig->getAvailableTemplates();
        array_walk(
            $options,
            function (&$option) {
                $option['label'] = $option['label'] . ' (' . $option['group'] . ')';
            }
        );
        usort(
            $options,
            function (array $firstElement, array $secondElement) {
                return strcmp($firstElement['label'], $secondElement['label']);
            }
        );

        return $options;
    }

    /**
     * Retrieve option array of variables
     *
     * @param array $origTemplateVariables
     * @return array
     */
    private function getVariablesOptionArray($origTemplateVariables): array
    {
        $optionArray = [];
        $variables = $this->parseVariablesString($origTemplateVariables);
        if ($variables) {
            foreach ($variables as $value => $label) {
                $optionArray[] = ['value' => '{{' . $value . '}}', 'label' => __('%1', $label)];
            }
            $optionArray = ['label' => __('Template Variables'), 'value' => $optionArray];
        }

        return $optionArray;
    }

    /**
     * Parse variables string into array of variables
     *
     * @param string $variablesString
     * @return array
     */
    private function parseVariablesString($variablesString): array
    {
        $variables = [];
        if ($variablesString && is_string($variablesString)) {
            $variablesString = str_replace("\n", '', $variablesString);
            $variables = $this->serializer->unserialize($variablesString);
        }

        return $variables;
    }
}
