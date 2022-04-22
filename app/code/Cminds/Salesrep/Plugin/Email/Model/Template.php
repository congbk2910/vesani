<?php

namespace Cminds\Salesrep\Plugin\Email\Model;

use Cminds\Salesrep\Helper\Data;
use Magento\Framework\Serialize\Serializer\Json;


/**
 * Cminds Salesrep email model template plugin.
 *
 * @category Cminds
 * @package  Cminds_Salesrep
 */
class Template
{
    /**
     * @var Json
     */
    private $serializer;
    
    /**
     * @var Data
     */
    private $salesrepHelper;

    /**
     * Template constructor.
     *
     * @param Json|null $serializer
     * @param Data $salesrepHelper
     */
    public function __construct(
        Json $serializer = null,
        Data $salesrepHelper
    ) {
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Json::class);
        $this->salesrepHelper = $salesrepHelper;
    }

    /**
     * Add custom variables to Insert Variable list
     *
     * @param \Magento\Email\Model\Template $subject
     * @param $withGroup
     *
     * @return array
     */
    public function beforeGetVariablesOptionArray( \Magento\Email\Model\Template $subject, $withGroup ){
        // limit to targeted template/templates
        if (
            true === (bool) $this->salesrepHelper->isModuleEnabled()
            && in_array( $subject->getOrigTemplateCode(), $this->salesrepHelper->getEmailCodesForVariableIsertion() )
        ) {
            $variablesString = $subject->getData('orig_template_variables');
            if ($variablesString && is_string($variablesString)) {
                $variablesString = str_replace("\n", '', $variablesString);
                $variables = $this->serializer->unserialize($variablesString);
                
                $variableCode = $this->salesrepHelper->getSalesrepNameCode();

                $variables["var {$variableCode}"] = __('Sales Representative Name');
                $variables["if {$variableCode}}} <" . __('your data, that depends on sales representative name here') . "> {{var {$variableCode}}} {{/if"] = __('Code Snippet for a block dependent on Sales Representative Name');

                $variables = $this->serializer->serialize($variables);
                $subject->setData('orig_template_variables', $variables);

            }
        }

        return [$withGroup];
    }


}