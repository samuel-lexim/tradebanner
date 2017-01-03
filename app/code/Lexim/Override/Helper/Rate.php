<?php
/**
 * @author Samuel Kong
 */

namespace Lexim\Override\Helper;

class Rate extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_collectionFactory;

    public $ratingList = [];

    /**
     * Rate constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Review\Model\ReviewFactory $reviewFactory
    )
    {
        $this->_storeManager  = $storeManager;
        $this->_reviewFactory = $reviewFactory;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }


    /**
     * Get Top Rate Products
     * @return array
     */
    public function getTopRateProducts() {
        $ratedAr = [];
        $this->ratingList = [];

        $products = $this->_collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter("status", "1")
            ->setOrder('created_at', 'DESC')
            ->load();

        foreach($products as $p ) {
            $score = $this->getRateScore($p);
            if ($score > 0) {
                $ratedAr[$p->getId()] = $score;

                $this->ratingList[$p->getId()] = array(
                    'score' => $score,
                    'star' => intval($score / 20),
                    'title' => $p->getName(),
                    'url' => $p->getProductUrl()
                );
            }
        }
        arsort($ratedAr);
        return array_slice($ratedAr, 0, 5, true);
    }

    /**
     * Get Rate Score
     * @param $product
     * @return int
     */
    public function getRateScore($product)
    {
        $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        $score = $product->getRatingSummary()->getRatingSummary();
        return intval($score);
    }

}