<?php

namespace Vsourz\Exitscreen\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


/*$content = '<div class="sitelogo">LOGO</div>
<h2>This Website requires you to be 18 years or older to enter.</h2>';
$content2 ='<div class="sitelogo">LOGO</div>
<h2>Sorry Adults Only</h2>';
*/

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */

    private $blockFactory;

    /**

     * @var \Magento\Cms\Model\BlockRepository
     */
    protected $blockRepository;


    /**
     * Construct
     *
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param \Magento\Cms\Model\BlockRepository $blockRepository
     */
    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\BlockRepository $blockRepository
    ) {
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
     public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
		
		$blocks = $this->blockFactory;

        $optionsBlock = $blocks->create()->getCollection()->getData();

        $blockIdentifierList = [];
        $blockExist;

        foreach ($optionsBlock as $optinIdfier) {
            $blockIdentifierList[] = $optinIdfier['identifier'];
        }
		
		if (in_array('vsourz-exitescreen', $blockIdentifierList)) {
            $blockExist = true;
        } else {
            $blockExist = false;
        }

        
        $setup->endSetup();
    }
}