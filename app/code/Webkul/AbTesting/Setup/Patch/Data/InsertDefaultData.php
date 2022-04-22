<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AbTesting
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\AbTesting\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Webkul\AbTesting\Model\TrackMasterFactory;

class InsertDefaultData implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var ControllersRepository
     */
    private $TrackMasterFactory;

   /**
    * @param ModuleDataSetupInterface $moduleDataSetup
    * @param TrackMasterFactory $trackMasterFactory
    */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        TrackMasterFactory $trackMasterFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->trackMasterFactory = $trackMasterFactory;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $data = [];
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        // key 1 is for magento default track type
        $trackTypes = [
            '1' => [
                'Click on review' => '#tab-label-reviews-title',
                'click on add review' => '.review-form-actions .submit',
                'Click on add to cart' => '#product-addtocart-button',
                'Click on compare' => '.tocompare',
                'Click on whislist' => '.action towishlist',
                'Email subscribed' => ".subscribe .actions .subscribe",
                'Clicked Signed In Button' => ".actions-toolbar .login",
                'Clicked Sign Up Button' => ".actions-toolbar .create",
                'Click on Home' => ".header .logo",
                'Forgot Password' => ".actions-toolbar .remind"
            ],
            '2' => [
                 'Track Pages Visit on' => "",
                'Click on element' => ""
            ]
        ];
        foreach ($trackTypes as $key => $data) {
            foreach ($data as $trackClass => $trackInfo) {
                $trackDb[] = [
                    'track_type_id' => $key,
                    'magento_selector_name' => $trackInfo,
                    'track_name' => $trackClass,
                ];
            }
        }

        $trackMaster =  $this->trackMasterFactory->create();
        $trackMaster->insertMultiple($trackDb, 'tracks_master');
        $trackMaster->save();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
