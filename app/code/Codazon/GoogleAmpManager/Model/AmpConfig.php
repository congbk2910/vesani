<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Model;

class AmpConfig
{
    const PROJECT_BASE_DIR = 'codazon/amp/less';
    
    const VARS_FILE = '_variables-custom.less';
    
    const DEST_CSS_DIR = 'codazon/amp/less/destination';
    
    const SRC_LESS_DIR = 'codazon/amp/less/source';
    
    const ENABLE_DEVELOPER_MODE_PATH = 'googleampmanager/developer_mode/enable';
    
    const LOGO_PATH = 'googleampmanager/general/logo';
    
    const LOGO_WIDTH_PATH = 'googleampmanager/general/logo_width';
    
    const LOGO_HEIGHT_PATH = 'googleampmanager/general/logo_height';
    
    const FOOTER_CONTENT_1_PATH = 'googleampmanager/footer/content_1';
    
    const FOOTER_CONTENT_2_PATH = 'googleampmanager/footer/content_2';
    
    public function getVariablesFile()
    {
        return self::PROJECT_BASE_DIR . '/' . self::VARS_FILE;
    }
    
    public function getProjectBaseDir()
    {
        return self::PROJECT_BASE_DIR;
    }
    
    public function getVariablesFileName()
    {
        return self::VARS_FILE;
    }
}
