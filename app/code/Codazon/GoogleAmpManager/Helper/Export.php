<?php
/**
* Copyright © 2019 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Helper;

use \Codazon\GoogleAmpManager\Model\AmpConfig;

use Codazon\GoogleAmpManager\Helper\Data as AmpHelper;

class Export extends \Codazon\GoogleAmpManager\Helper\Import
{

    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }
    
    
    protected function getConfigSection()
    {
        return 'googleampmanager';
    }
    
    protected function getConnection()
    {
        if ($this->connection === null) {
            return $this->objectManager->get(\Magento\Cms\Model\ResourceModel\Page\Collection::class)->getConnection();
        }
        return $this->connection;
    }
    
    protected function makupTitle($title)
    {
        return "<h3 style='font-family: sans-serif;color: #00BCD4;font-weight: 400;'>$title</h3>";
    }
    
    public function exportDefaultConfigFile()
    {
        $messages = [];
        $messages[] = $this->makupTitle('Export Settings');
        $section = $this->getConfigSection();
        
        $connection = $this->getConnection();
        $xmlParser = $this->objectManager->create(\Magento\Framework\Xml\Parser::class);
        $xmlGenerator = $this->objectManager->create(\Magento\Framework\Xml\Generator::class);
                
        $moduleReader = $this->getModuleReader();
        $moduleDir = $moduleReader->getModuleDir('etc', 'Codazon_GoogleAmpManager');
        $configFile = $moduleDir . '/config.xml';
        $xmlParser->load($configFile);
        $configArray = $xmlParser->xmlToArray();
        
        
        $stylesHelper = $this->getStylesHelper();
        $variablesFile = $stylesHelper->getMediaDir(AmpConfig::PROJECT_BASE_DIR . '/_variables-default.less');
        $parser = new \Less_Parser();
        $parser->parseFile($variablesFile, '');
        $variables = $parser->getVariables();
        $configVariables = [];
        
        foreach ($variables as $name => $value) {
            $name = str_replace('@', '', $name);
            $value = trim(str_replace("'", "", $value));
            $configVariables[$name] = $value;
        }
        $configArray['config']['_attribute'] = [
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:noNamespaceSchemaLocation' => 'urn:magento:module:Magento_Store:etc/config.xsd',
        ];
        
        $table = $connection->getTableName('core_config_data');
        $deleteRows = [];
        $connection->delete($table, ["path = 'googleampmanager/styles/variables' AND scope = 'default' AND scope_id=0"]);
        
        foreach ($configArray['config']['_value']['default'][$section] as $groupName => $group) {
            foreach ($group as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $subField => $subValue) {
                        $path = "{$section}/{$groupName}/{$field}/{$subField}";
                        if (isset($configVariables[$subField])) {
                            $configArray['config']['_value']['default'][$section][$groupName][$field][$subField] = $configVariables[$subField];
                        } else {
                            $configArray['config']['_value']['default'][$section][$groupName][$field][$subField] = $this->getConfig("{$section}/{$groupName}/{$field}/{$subField}");
                        }
                        if ($connection->delete($table, ["path = '{$path}' AND scope = 'default' AND scope_id=0"])) {
                            $deleteRows[$path] = $configArray['config']['_value']['default'][$section][$groupName][$field][$subField];
                        }
                    }
                } else {
                    $path = "{$section}/{$groupName}/{$field}";
                    if (isset($configVariables[$field])) {
                        $configArray['config']['_value']['default'][$section][$groupName][$field] = $configVariables[$field];
                    } else {
                        $configArray['config']['_value']['default'][$section][$groupName][$field] = $this->getConfig("{$path}");
                    }
                    if ($connection->delete($table, ["path = '{$path}' AND scope = 'default' AND scope_id=0"])) {
                        $deleteRows[$path] = $configArray['config']['_value']['default'][$section][$groupName][$field];
                    }
                }
            }
        }
        
        $messages[] = empty($deleteRows) ? "No settings changed" : "Changed settings (made it to default): ";
        
        foreach ($deleteRows as $path => $value) {
            $value = strip_tags($value);
            $messages[] = "+ Deleted <span style='background: #ffedb8; padding: 2px 5px'>{$path}</span> from <span style='background: #cddc39; padding: 2px 5px'>{$table}</span> with value <span style='background: #65cfff; padding: 2px 5px; font: small-caption'>{$value}</span>, scope default.";
        }
        
        $xml = $xmlGenerator->arrayToXml($configArray);
        $xml = str_replace('<config xmlns:xsi', '<!--
/**
 * Copyright © 2020 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */
-->' . "\n<config xmlns:xsi", $xml);
        $stylesHelper->write($configFile, $xml);
        
        return [
            'messages' => $messages
        ];
    }
    
    public function exportLessDefaultVariablesFile()
    {
        $messages = [];
        $messages[] = $this->makupTitle('Export Less Default Variables File');
        $stylesHelper = $this->getStylesHelper();
        $modifiedVariables = json_decode(($this->getConfig('googleampmanager/styles/variables') ? : '{}'), true);
        $variables = [];
        foreach ($modifiedVariables as $name => $value) {
            $variables[$name] = (stripos($name, 'font') === false) ? $value : "~'{$value}'";
        }
        $variablesFile = $stylesHelper->getMediaDir(AmpConfig::PROJECT_BASE_DIR . '/_variables-default.less');
        $parser = new \Less_Parser();
        $parser->parseFile($variablesFile, '');
        $parser->ModifyVars($variables);
        $newVariables = $parser->getVariables();
        $output = '';
        foreach ($newVariables as $name => $value) {
            $newValue = trim((stripos($value, "'") === false) ? $value : "~{$value}");
            $output .= "{$name}: {$newValue};\n";
        }
        $stylesHelper->write($variablesFile, $output);
        $destination = $stylesHelper->getMediaDir(AmpConfig::DEST_CSS_DIR);
        $stylesHelper->getIo()->rmdirRecursive($destination);
        $stylesHelper->getIo()->mkdir($destination);
        $messages[] = "Variables file: <span style='background: #c1ff79;padding: 2px 10px;font-family: sans-serif;font-size: 12px;'>$variablesFile</span>";
        $messages[] = "Empty CSS output directory: <span style='background: #c1ff79;padding: 2px 10px;font-family: sans-serif;font-size: 12px;'>$destination</span>";
        return [
            'messages' => $messages
        ];
    }
    
    protected function getHomepageId()
    {
        $homepage = $this->getConfig('web/default/cms_home_page');
        $cmsModel = $this->objectManager->create(\Magento\Cms\Model\Page::class)->load($homepage, 'identifier');
        return $cmsModel->getId();
    }
        
    public function exportCmsPageAmp()
    {
        $messages = [];
        $messages[] = $this->makupTitle('Export CMS Page AMP');
        $homepageId = $this->getHomepageId();
        $collection = $this->objectManager->create(\Codazon\GoogleAmpManager\Model\ResourceModel\Page\Collection::class);
        $header = ['amp_content', 'options', 'is_home_page', 'identifier'];
        $rows[] = $header;
        
        $exported = [];
        foreach ($collection->getItems() as $item) {
            $itemData = [];
            foreach ($header as $column) {
                $page = $this->objectManager->create(\Magento\Cms\Model\Page::class)->load($item->getPageId());
                if ($column == 'is_home_page') {
                    $itemData[$column] = (int)($item->getPageId() == $homepageId);
                } elseif ($column == 'identifier') {
                    $itemData[$column] = $page->getData('identifier');
                } else {
                    $itemData[$column] = $item->getData($column);
                }
            }
            $rows[] = $itemData;
            $exported[] = '('. $page->getId() . ') <strong>'. $page->getTitle() . '</strong> <sup>(AMP ID: ' . $item->getId() . ')</sup>';
        }
        $file = $this->getFixtureManager()->getFixture(self::PAGE_AMP_FIXTURE);
        $this->getCsvReader()->saveData($file, $rows);
        $messages[] = "Fixture file: <span style='background: #c1ff79;padding: 2px 10px;font-family: sans-serif;font-size: 12px;'>$file</span>";
        foreach ($exported as $page) {
            $messages[] = "+ Exported AMP for <span style='background: #ffedb8; padding: 5px 5px 2px'>{$page}</span>";
        }
        return [
            'messages' => $messages
        ];
    }
}