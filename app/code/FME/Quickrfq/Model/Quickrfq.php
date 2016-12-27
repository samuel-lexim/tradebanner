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

    public function getMaterialCustomEs()
    {
        $options = array('13oz Scrim Vinyl   - Matte' => '13oz Scrim Vinyl   - Matte',
            '13oz Scrim Vinyl   - Gloss' => '13oz Scrim Vinyl   - Gloss',
            '14oz Scrim Vinyl   - Matte' => '14oz Scrim Vinyl   - Matte',
            '14oz Scrim Vinyl   - Gloss' => '14oz Scrim Vinyl   - Gloss',
            '16oz Blockout Banner' => '16oz Blockout Banner',
            'Backlit Banner' => 'Backlit Banner',
            'Fabric Banner - Premium' => 'Fabric Banner - Premium',
            'Fabric Banner- Deluxe' => 'Fabric Banner- Deluxe',
            'Mesh Banner' => 'Mesh Banner',
            'Premium Coated Banner' => 'Premium Coated Banner',
            'SuperSmooth Banner' => 'SuperSmooth Banner',
            'Window Vinyl Decal - White Gloss' => 'Window Vinyl Decal - White Gloss',
            'Window Vinyl Decal - White Matte' => 'Window Vinyl Decal - White Matte',
            'Window Vinyl Decal - Clear' => 'Window Vinyl Decal - Clear',
            'Window Static Cling - White' => 'Window Static Cling - White',
            'Backlit Film' => 'Backlit Film',
            'Canvas' => 'Canvas',
            'Car Magnets' => 'Car Magnets',
            'Digital Poster Gloss' => 'Digital Poster Gloss',
            'Digital Poster Matte' => 'Digital Poster Matte',
            'Polyropylene (PET)' => 'Polyropylene (PET)',
            'Acrylic Boards' => 'Acrylic Boards',
            'Aluminum Sandwhich Board (Dibond)' => 'Aluminum Sandwhich Board (Dibond)',

        );

        return $options;
    }

}