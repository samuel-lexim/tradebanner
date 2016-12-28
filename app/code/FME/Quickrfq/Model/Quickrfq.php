<?php

namespace FME\Quickrfq\Model;


class Quickrfq extends \Magento\Framework\Model\AbstractModel
{


    protected function _construct()
    {
        $this->_init('FME\Quickrfq\Model\ResourceModel\Quickrfq');
    }


    public function getAvailableStatuses()
    {
        $availableOptions = array('New' => 'New',
            'Under Process' => 'Under Process',
            'Pending' => 'Pending',
            'Done' => 'Done');

        return $availableOptions;
    }

    public function getBudgetStatuses()
    {
        $options = array('Hemming' => 'Hemming',
            'Grommets' => 'Grommets',
            'Pole Pockets' => 'Pole Pockets',
            'Wind Holes' => 'Wind Holes');

        return $options;
    }

    public function getCategoryCustomEs()
    {
        $options = array('Banners' => 'Banners',
            'Adhesive' => 'Adhesive',
            'Digital Print' => 'Digital Print',
            'Signs and Boards' => 'Signs and Boards',
            'Banner Stand' => 'Banner Stand',
            'Car Wraps' => 'Car Wraps',
            'Lettering (Die Cut Vinyl - Plotter)' => 'Lettering (Die Cut Vinyl - Plotter)',
            'Others' => 'Others');

        return $options;
    }

    public function getMaterialCustomEs_01()
    {
        $options = array('N/A' => 'N/A',
            '13oz Scrim Vinyl   - Matte' => '13oz Scrim Vinyl   - Matte',
            '13oz Scrim Vinyl   - Gloss' => '13oz Scrim Vinyl   - Gloss',
            '14oz Scrim Vinyl   - Matte' => '14oz Scrim Vinyl   - Matte',
            '14oz Scrim Vinyl   - Gloss' => '14oz Scrim Vinyl   - Gloss',
            '14oz Scrim Vinyl   - Matte' => '14oz Scrim Vinyl   - Matte',
            '16oz Blockout Banner' => '16oz Blockout Banner',
            'Backlit Banner' => 'Backlit Banner',
            'Fabric Banner - Premium' => 'Fabric Banner - Premium',
            'Fabric Banner - Deluxe' => 'Fabric Banner - Deluxe',
            'Mesh Banner' => 'Mesh Banner',
            'Premium Coated Banner' => 'Premium Coated Banner',
            'SuperSmooth Banner' => 'SuperSmooth Banner');

        return $options;
    }

}