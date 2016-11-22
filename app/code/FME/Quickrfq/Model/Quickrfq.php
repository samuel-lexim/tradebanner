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
        $options = array('Backlit Film' => 'Backlit Film',
            'Canvas' => 'Canvas',
            'Car Magnets' => 'Car Magnets',
            'Digital Poster Gloss' => 'Digital Poster Gloss',
            'Digital Poster Matte' => 'Digital Poster Matte',
            'Polyropylene (PET)' => 'Polyropylene (PET)');

        return $options;
    }

}