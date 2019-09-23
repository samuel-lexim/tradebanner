<?php

namespace FME\Pricecalculator\Observer;


class UpdatePriceCart implements \Magento\Framework\Event\ObserverInterface
{

    protected $_request;
    protected $_pcHelper;
    protected $_objectManager;
    protected $logger;


    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \FME\Pricecalculator\Helper\Data $_pcHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->_request = $request;
        $this->_pcHelper = $_pcHelper;
        $this->_objectManager = $objectManager;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
      
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $_product = $observer->getProduct();
        $customOptions = $_product->getOptions();
        $opsAr = [];

        $totalOp = 0;
        $id = $_product->getId();
        // $unitPrice = $_product->getPriceUnitArea();

        if ($customOptions && $_product->getPricingLimit() != '') {
            $fieldOptions = $this->_pcHelper->getFieldOptions($_product);
            if ($fieldOptions) {
                foreach ($customOptions as $option) {
                    $opsAr[$option->getId()] = $option;
                    if (isset($fieldOptions[$option->getTitle()])) {
                        $totalOp++;
                    }
                }
            }
        }



        // if ($totalOp == 0) return; // If not exists width, height then break

        $params = $this->_request->getParams();
        // {"uenc":"aHR0cDovLzUyLjM5LjQyLjgvZmxvb3ItZ3JhcGhpY3MuaHRtbA,,","product":"123","selected_configurable_option":"","related_product":"","form_key":"E69AxhEPz3TTpxAE","options":{"503":"45","504":"55","505":"897","506":"898","507":"899","508":"900","509":"12","1028":"1691","1029":"","510":"123"},"qty":"1","zip-code":""}
        //$this->logger->debug("= params ==" . json_encode($params));

        $posted_options = $params['options']; // { 'select id' => 'option id' }
        // {"318":"44","319":"66","316":"594","317":"595","320":"598","680":"1136","321":"605","322":"606","323":"609","324":"615","325":"618","326":"22","740":"asdsad"}


        // Calc price foreach products - Flags
        $has15 = false;
        $has17 = false;
        $has175 = false;
        $has2 = false;
        $has34 = false;
        // $notFixAreaPrice = true;
        $hasDiscount = true;
        $areaPrice = 0;
        $area = $this->_calculateArea($_product, $posted_options);


        /** Banner */
        if ($id == 90) { // 13oz Vinyl Banner
            
            $turn = $posted_options[1742];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 3027) $has17 = true;

            $areaPrice = $area * 0.89 / 144;

            $matOpId = $posted_options[1729];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            if ($matOpId == 2993) $hasDiscount = false;

        } else if ($id == 91) { // 14oz Vinyl Banner
            
            $turn = $posted_options[1761];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 3068) $has17 = true;

            $areaPrice = $area * 0.89 / 144;

            $matOpId = $posted_options[1748];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            if ($matOpId == 3034) $hasDiscount = false;

        } else if ($id == 92) { // 16 oz Blockout Banner
            
            $areaPrice = $area * 1.5 / 144;

            $turn = $posted_options[1460];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 2464) $has17 = true;

        } else if ($id == 93) { // Backlit Banner
            
            $areaPrice = $area * 3.99 / 144;

            $turn = $posted_options[1473];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 2484) $has17 = true;

        } else if ($id == 94) { // Double Sided Banner (Super Smooth)
            
            $matOpId = $posted_options[1569];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            $matObj = $opsAr[1569]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($area / 144* $matPrice) - $matPrice;

            $turn = $posted_options[1576];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 2699) $has17 = true;

        } else if ($id == 95) { // Fabric Banner- Premium
            
            $areaPrice = $area * 1.49 / 144;

            $turn = $posted_options[353];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 675) $has17 = true;

        } else if ($id == 96) { // Mesh Banner
            
            $areaPrice = $area * 1.5 / 144;

            $turn = $posted_options[1505];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 2550) $has17 = true;

        } else if ($id == 97) { // Premium Coated Banner
            
            $areaPrice = $area * 1.39 / 144;

            $turn = $posted_options[1883];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 3254) $has17 = true;

        } else if ($id == 98) { // Super Smooth Banner
            
            $matOpId = $posted_options[1585];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            $matObj = $opsAr[1585]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($area / 144 * $matPrice) - $matPrice;

            $turn = $posted_options[1592];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 2735) $has17 = true;

        } else if ($id == 99) { // Fabric Banner- Deluxe
            
            $areaPrice = $area * 3.49 / 144;

            $turn = $posted_options[362];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 694) $has17 = true;

        } else if ($id == 100) {
            /** Adhesives */ // 3M Controltac (IJ 180C)
            
            $laminationId = $posted_options[402];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[402]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($area / 144 * (2.5 + $lamPrice)) - $lamPrice;

            $turn = $posted_options[403];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 770) $has17 = true;

        } else if ($id == 101) { // 3M IJ35 Adhesive Vinyl
            
            $laminationId = $posted_options[410];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[410]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $matOpId = $posted_options[408];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            $matObj = $opsAr[408]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($area / 144 * ($matPrice + $lamPrice)) - $lamPrice - $matPrice;

            $turn = $posted_options[411];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 778) $has17 = true;

        } else if ($id == 102) { // Bumper Stickers
            
            // $areaPrice = 0; dont need $areaPrice

            $turn = $posted_options[418];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 788) $has17 = true;

        } else if ($id == 123) { // Floor Graphics
            

            $matOpId = $posted_options[505];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            $matObj = $opsAr[505]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($area / 144 * $matPrice ) - $matPrice;
            $turn = $posted_options[508];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 901) $has17 = true;

        } else if ($id == 126) { // Perforated Stickers (One-Way Vision)
            
            $matOpId = $posted_options[498];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            $matObj = $opsAr[498]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($area / 144 * $matPrice) - $matPrice;

            $turn = $posted_options[500];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 896) $has17 = true;

        } else if ($id == 127) { // Static Clings (Window Clings)
            
            $matOpId = $posted_options[491];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            $matObj = $opsAr[491]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($area / 144 * $matPrice) - $matPrice;

            $turn = $posted_options[493];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 892) $has17 = true;

        } else if ($id == 128) { // Vinyl Stickers (Window Decal)
            
            $matOpId = $posted_options[1838];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            $matObj = $opsAr[1838]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $laminationId = $posted_options[1840];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[1840]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($area / 144 * ($matPrice + $lamPrice)) - $matPrice - $lamPrice;

            $turn = $posted_options[1841];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 3176) $has17 = true;

        } else if ($id == 129) { // Wall Vinyl Decals
            
            $matOpId = $posted_options[475];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            $matObj = $opsAr[475]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $laminationId = $posted_options[477];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[477]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($area / 144 * ($matPrice + $lamPrice)) - $matPrice - $lamPrice;

            $turn = $posted_options[478];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 877) $has2 = true;

        } else if ($id == 103) {
            /** Digital Prints */ // Backlit Film
            
            $laminationId = $posted_options[426];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[426]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($area / 144 * (3.5 + $lamPrice)) - $lamPrice;

            $turn = $posted_options[427];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 796) $has17 = true;

        } else if ($id == 104) { // Canvas Roll
            
            $matOpId = $posted_options[430];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            $matObj = $opsAr[430]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            if ($matOpId == 798) $hasDiscount = false;
            $areaPrice = ($area / 144 * $matPrice) - $matPrice;

            $turn = $posted_options[434];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 801) $has17 = true;

            if ($matOpId == 798) $hasDiscount = false;

        } else if ($id == 130) { // Polypropylene (PET)
            
            $laminationId = $posted_options[516];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[516]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($area / 144 * (2.5 + $lamPrice)) - $lamPrice;

            $turn = $posted_options[515];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 905) $has17 = true;

        } else if ($id == 131) { // Digital Poster
            
            $laminationId = $posted_options[524];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[524]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($area / 144 * $lamPrice) - $lamPrice;

            $turn = $posted_options[525];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 921) $has17 = true;

        } else if ($id == 132) { // Car Magnets
            
            $laminationId = $posted_options[532];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[532]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($area / 144 * $lamPrice) - $lamPrice;

            $turn = $posted_options[534];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 931) $has17 = true;

        } else if ($id == 155) { // Framed Canvas
            

            $widthVal = $posted_options[811];
            $widthVal = is_null($widthVal) ? 0 : $widthVal;

            $heightVal = $posted_options[812];
            $heightVal = is_null($heightVal) ? 0 : $heightVal;

            $goodWidth = ($widthVal == 12 || $widthVal == 18 || $widthVal == 24 || $widthVal == 30 || $widthVal == 36 || $widthVal == 48);
            $goodHeight = ($heightVal == 12 || $heightVal == 18 || $heightVal == 24 || $heightVal == 30 || $heightVal == 36 || $heightVal == 48);

            $turn = $posted_options[815];
            if (is_array($turn)) $turn = $turn[0];

            if ($goodWidth && $goodHeight) {
                $areaPrice = $area * 8 / 144;
                if ($turn == 1305) $areaPrice *= 2;
            } else $areaPrice = $area * 9 / 144;

            if ($turn == 1303) $has15 = true; // next day: 1.5 --- 2 day: 1
            else if ($turn == 1305) $has17 = true; // same day: 1.7

        } else if ($id == 106) {
            /** SIGNS & BOARDS */ // Acrylic Boards
            

            $laminationId = $posted_options[448];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[448]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $matOpId = $posted_options[447];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            $matObj = $opsAr[447]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $drillOpId = $posted_options[449];
            if (is_array($drillOpId)) $drillOpId = $drillOpId[0];
            $drillObj = $opsAr[449]->getValues();
            $drillPrice = $drillObj[$drillOpId]->getPrice();
            $drillPrice = is_null($drillPrice) ? 0 : $drillPrice;

            $areaPrice = ($area / 144 * ($lamPrice + $matPrice)) - $lamPrice - $matPrice;

            $turn = $posted_options[451];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 834) $areaPrice = ($area / 144 * ($lamPrice + $matPrice) + $drillPrice) * 1.7 - $lamPrice - $matPrice - $drillPrice;

        } else if ($id == 107) { // Aluminum Sandwich Board “Dibond”
            

            $laminationId = $posted_options[458];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[458]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $colorOpId = $posted_options[456];
            if (is_array($colorOpId)) $colorOpId = $colorOpId[0];
            if ($colorOpId == 835) { // 4:0- Full Color on Front Side Only
                $matOpId = $posted_options[457];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[457]->getValues();
                $lam2Price = $lamPrice;
            } else { // 4:4 - Full Color on Both Sides
                $matOpId = $posted_options[870];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[870]->getValues();
                $lam2Price = $lamPrice * 2;
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = $area / 144 * ($lam2Price + $matPrice) - $lamPrice - $matPrice;

            $turn = $posted_options[460];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 846) $has15 = true;
            else if ($turn == 848) $has175 = true;

        } else if ($id == 108) { // Aluminum Sheets
            $size = 0;
            $normal = false;

            // Get size id
            $sizeId = $posted_options[1139];
            if (is_array($sizeId)) $sizeId = $sizeId[0];


            // Get color id
            $colorId = $posted_options[464];
            if (is_array($colorId)) $colorId = $colorId[0];

            // Get turnaround
            $turn = $posted_options[469];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 867) $has15 = true; // next day: 1.5 --- 2 day: 1
            else if ($turn == 868) $has175 = true; // same day: 1.75

            // Get Lam id
            $lamId = $posted_options[468];
            if (is_array($lamId)) $lamId = $lamId[0];
            $lamPrice = 0;
            if ($sizeId == 1779) {
                if ($colorId == 850) { // 4:0- Full Color on Front Side Only
                    $size = 7.5;
                    if ($lamId == 863) $lamPrice = 1.2;
                    else if ($lamId == 864) $lamPrice = 1.21;
                    else if ($lamId == 865) $lamPrice = 3.75;
                } else { // 4:4 - Full Color on Both Sides
                    $size = 11.25;
                    if ($lamId == 863) $lamPrice = 2.4;
                    else if ($lamId == 864) $lamPrice = 2.41;
                    else if ($lamId == 865) $lamPrice = 7.5;
                }

            } else if ($sizeId == 1780) {
                if ($colorId == 850) { // 4:0- Full Color on Front Side Only
                    $size = 15;
                    if ($lamId == 863) $lamPrice = 2.4;
                    else if ($lamId == 864) $lamPrice = 2.41;
                    else if ($lamId == 865) $lamPrice = 7.5;
                } else { // 4:4 - Full Color on Both Sides
                    $size = 22.5;
                    if ($lamId == 863) $lamPrice = 4.8;
                    else if ($lamId == 864) $lamPrice = 4.81;
                    else if ($lamId == 865) $lamPrice = 15;
                }

            } else if ($sizeId == 1781) {
                if ($colorId == 850) { // 4:0- Full Color on Front Side Only
                    $size = 30;
                    if ($lamId == 863) $lamPrice = 4.8;
                    else if ($lamId == 864) $lamPrice = 4.81;
                    else if ($lamId == 865) $lamPrice = 15;
                } else { // 4:4 - Full Color on Both Sides
                    $size = 45;
                    if ($lamId == 863) $lamPrice = 9.6;
                    else if ($lamId == 864) $lamPrice = 9.61;
                    else if ($lamId == 865) $lamPrice = 30;
                }

            } else { // Custom
                $normal = true;

                $widthVal = $posted_options[1140];
                $widthVal = is_null($widthVal) ? 0 : $widthVal;
                $heightVal = $posted_options[1141];
                $heightVal = is_null($heightVal) ? 0 : $heightVal;

                if ($lamId == 863) $lamPrice = 0.8;
                else if ($lamId == 864) $lamPrice = 0.81;
                else if ($lamId == 865) $lamPrice = 2.5;

                $lamPrice = ($colorId == 850) ? $lamPrice + 6 : $lamPrice * 2 + 10;
                if ($turn == 868 && $colorId != 850) $lamPrice -= 4;

                $areaPrice = $widthVal * $heightVal / 144 * $lamPrice;
            }

            // Set price
            if (!$normal) $areaPrice = $size + $lamPrice;

        } else if ($id == 146) { // Arrow Spinner Signs
            
            $colorId = $posted_options[744];
            if (is_array($colorId)) $colorId = $colorId[0];
            $sizeId = $posted_options[973];
            if (is_array($sizeId)) $sizeId = $sizeId[0];
            $lamId = $posted_options[974];
            if (is_array($lamId)) $lamId = $lamId[0];

            if ($colorId == 1216) { // 4:0- Full Color on Front Side Only
                if ($sizeId == 1579) { //18 Inches x 48 Inches
                    $sizeVal = 18.50;

                    if ($lamId == 1582) $lamVal = 0;
                    else if ($lamId == 1583) $lamVal = 4.8;
                    else if ($lamId == 1584) $lamVal = 4.81;
                    else $lamVal = 15;

                } else if ($sizeId == 1580) { // 20 Inches x 60 Inches
                    $sizeVal = 23.75;

                    if ($lamId == 1582) $lamVal = 0;
                    else if ($lamId == 1583) $lamVal = 6.67;
                    else if ($lamId == 1584) $lamVal = 6.68;
                    else  $lamVal = 20.83;

                } else { // 24 Inches  x 72 Inches
                    $sizeVal = 32;

                    if ($lamId == 1582) $lamVal = 0;
                    else if ($lamId == 1583) $lamVal = 9.6;
                    else if ($lamId == 1584) $lamVal = 9.61;
                    else  $lamVal = 30;
                }

            } else { // 4:4 - Full Color on Both Sides
                if ($sizeId == 1579) { //18 Inches x 48 Inches
                    $sizeVal = 28;

                    if ($lamId == 1582) $lamVal = 0;
                    else if ($lamId == 1583) $lamVal = 9.6;
                    else if ($lamId == 1584) $lamVal = 9.61;
                    else  $lamVal = 30;

                } else if ($sizeId == 1580) { // 20 Inches x 60 Inches
                    $sizeVal = 37;

                    if ($lamId == 1582) $lamVal = 0;
                    else if ($lamId == 1583) $lamVal = 13.34;
                    else if ($lamId == 1584) $lamVal = 13.35;
                    else  $lamVal = 41.66;

                } else { // 24 Inches  x 72 Inches
                    $sizeVal = 50;

                    if ($lamId == 1582) $lamVal = 0;
                    else if ($lamId == 1583) $lamVal = 19.2;
                    else if ($lamId == 1584) $lamVal = 19.21;
                    else  $lamVal = 60;

                }
            }

            $areaPrice = $sizeVal + $lamVal;

            $turn = $posted_options[747];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1226) $has17 = true;

        } else if ($id == 147) { // Yard Signs (Coroplast)
            

            $matOpId = $posted_options[1663];
            if (is_array($matOpId)) $matOpId = $matOpId[0];

            if ($matOpId == 2861) {
                $colorId = $posted_options[1665];
                $colorObj = $opsAr[1665]->getValues();
            } else {
                $colorId = $posted_options[1664];
                $colorObj = $opsAr[1664]->getValues();
            }

            $colorPrice = $colorObj[$colorId]->getPrice();
            $colorPrice = is_null($colorPrice) ? 1 : $colorPrice;

            $areaPrice = ($area / 144 * $colorPrice) - $colorPrice;

            $turn = $posted_options[1668];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 2875) $has17 = true;

        } else if ($id == 148) { // Ultra(Gator) Board            

            $laminationId = $posted_options[626];
        
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[626]->getValues();

            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;


            $groId = $posted_options[627];

            if (is_array($groId)) $groId = $groId[0];
            $groObj = $opsAr[627]->getValues();

            if (isset($groObj[$groId])) $groPrice = $groObj[$groId]->getPrice();
            else $groPrice = 0;
            $groPrice = is_null($groPrice) ? 0 : $groPrice;

            $colorId = $posted_options[624];

            if (is_array($colorId)) $colorId = $colorId[0];
            if ($colorId == 1049) {
                $matOpId = $posted_options[625];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[625]->getValues();

                $matPrice = $matObj[$matOpId]->getPrice();
                $matPrice = is_null($matPrice) ? 0 : $matPrice;

                $areaPrice = ($area / 144 * ($lamPrice + $matPrice)) - $lamPrice - $matPrice;

            } else {
                $matOpId = $posted_options[888];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[888]->getValues();

                $matPrice = $matObj[$matOpId]->getPrice();
                $matPrice = is_null($matPrice) ? 0 : $matPrice;

                $areaPrice = ($area / 144 * ($lamPrice * 2 + $matPrice)) - $lamPrice - $matPrice;
            }

    

            if ($matOpId == 1053 || $matOpId == 1452) $areaPrice -= $groPrice;


            $turn = $posted_options[628];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 1064) $has17 = true;

        } else if ($id == 149) { // Styrene Boards
            

            $laminationId = $posted_options[635];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[635]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $groId = $posted_options[636];
            if (is_array($groId)) $groId = $groId[0];
            $groObj = $opsAr[636]->getValues();
            $groPrice = $groObj[$groId]->getPrice();
            $groPrice = is_null($groPrice) ? 0 : $groPrice;

            // code
            $colorId = $posted_options[633];
            if (is_array($colorId)) $colorId = $colorId[0];
            if ($colorId == 1065) {
                $matOpId = $posted_options[634];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[634]->getValues();
                $lam2Price = $lamPrice;
            } else {
                $matOpId = $posted_options[1879];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[1879]->getValues();
                $lam2Price = $lamPrice * 2;
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            // and code
            $turn = $posted_options[638];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1079) {
                $areaPrice = ($area / 144 * ($lam2Price + $matPrice) + $groPrice) * 1.7 - $groPrice - $lamPrice - $matPrice;
            } else {
                $areaPrice = ($area / 144 * ($lam2Price + $matPrice)) - $lamPrice - $matPrice;
            }

        } else if ($id == 150) { // PVC Sintra Board
            

            $laminationId = $posted_options[645];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[645]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $groId = $posted_options[646];
            if (is_array($groId)) $groId = $groId[0];
            $groObj = $opsAr[646]->getValues();
            $groPrice = $groObj[$groId]->getPrice();
            $groPrice = is_null($groPrice) ? 0 : $groPrice;

            $colorId = $posted_options[643];
            if (is_array($colorId)) $colorId = $colorId[0];
            if ($colorId == 1080) {
                $matOpId = $posted_options[644];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[644]->getValues();
                $lam2Price = $lamPrice;
            } else {
                $matOpId = $posted_options[885];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[885]->getValues();
                $lam2Price = $lamPrice * 2;
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $turn = $posted_options[648];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1097) {
                $areaPrice = ($area / 144 * ($lam2Price + $matPrice) + $groPrice) * 1.7 - $groPrice - $lamPrice - $matPrice;
            } else {
                $areaPrice = ($area / 144 * ($lam2Price + $matPrice)) - $lamPrice - $matPrice;
            }

        } else if ($id == 151) { // Foam Boards
            

            $laminationId = $posted_options[1889];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[1889]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $colorId = $posted_options[653];
            if (is_array($colorId)) $colorId = $colorId[0];
            if ($colorId == 1098) {
                $matOpId = $posted_options[654];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[654]->getValues();
            } else {
                $matOpId = $posted_options[1888];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[1888]->getValues();
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            if ($colorId == 1098) {
                $areaPrice = $area / 144 * ($lamPrice + $matPrice) - $lamPrice - $matPrice;
            } else {
                $areaPrice = $area / 144 * ($lamPrice * 2 + $matPrice) - $lamPrice - $matPrice;
            }

            $turn = $posted_options[1891];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 3272) $has17 = true;

        } else if ($id == 152) { // Eagle Board (Eco Board)
            

            $laminationId = $posted_options[664];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[664]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $colorId = $posted_options[662];
            if (is_array($colorId)) $colorId = $colorId[0];
            if ($colorId == 1113) {
                $matOpId = $posted_options[663];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[663]->getValues();
            } else {
                $matOpId = $posted_options[881];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[881]->getValues();
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            if ($colorId == 1113) {
                $areaPrice = $area / 144 * ($lamPrice + $matPrice) - $lamPrice - $matPrice;
            } else {
                $areaPrice = $area / 144 * ($lamPrice * 2 + $matPrice) - $lamPrice - $matPrice;
            }

            $turn = $posted_options[665];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1121) $has17 = true;

        } else if ($id == 153) { // Converd Coated Board
            

            $laminationId = $posted_options[672];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[672]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $colorId = $posted_options[670];
            if (is_array($colorId)) $colorId = $colorId[0];
            if ($colorId == 1122) {
                $matOpId = $posted_options[671];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[671]->getValues();
            } else {
                $matOpId = $posted_options[879];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[879]->getValues();
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            if ($colorId == 1122) {
                $areaPrice = $area / 144 * ($lamPrice + $matPrice) - $lamPrice - $matPrice;
            } else {
                $areaPrice = $area / 144 * ($lamPrice * 2 + $matPrice) - $lamPrice - $matPrice;
            }

            $turn = $posted_options[674];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1134) $has17 = true;

        } else if ($id == 110) {
            /** Banner stands */  // Mini Banner Stands
            
            $areaPrice = 0;

            $turn = $posted_options[1869];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 3234) $has17 = true;

        } else if ($id == 111) { // Outdoor Banner Stand
            
            $areaPrice = 0;

            $turn = $posted_options[765];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1247) $has17 = true;

        } else if ($id == 112) { // Outdoor Double Banner Stand
            

            $matOpId = $posted_options[770];
            if (is_array($matOpId)) $matOpId = $matOpId[0];
            $matObj = $opsAr[770]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $turn = $posted_options[774];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1257) { // Same day
                $areaPrice = $matPrice * 1.7 - 80 - $matPrice;
            } else {
                $areaPrice = 0;
            }

        } else if ($id == 141) { // X-Frame with Supersmooth
            

            $sizeId = $posted_options[963];
            if (is_array($sizeId)) $sizeId = $sizeId[0];

            $w = $posted_options[965];
            if (is_array($w)) $w = $w[0];
            $h = $posted_options[966];
            if (is_array($h)) $h = $h[0];
            $w = is_null($w) ? 0 : floatval($w);
            $h = is_null($h) ? 0 : floatval($h);

            $turn = $posted_options[808];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1300) { // Same day
                if ($sizeId == 1569) $areaPrice = 15.35;
                else if ($sizeId == 1570) $areaPrice = 21.94;
                else if ($sizeId == 1571) $areaPrice = 31.69;
                else $areaPrice = $w * $h * 3.41 / 144;
            } else {
                $areaPrice = 0;
                if ($sizeId == 1572 || $sizeId == 1573) $areaPrice = $w * $h * 1.95 / 144;
            }

            if ($sizeId == 1572) $areaPrice += 15;
            else if ($sizeId == 1573) $areaPrice += 23;

        } else if ($id == 142) { // X-Frame with Polypropylene (PET)
            

            $sizeId = $posted_options[964];
            if (is_array($sizeId)) $sizeId = $sizeId[0];

            $w = $posted_options[969];
            if (is_array($w)) $w = $w[0];
            $h = $posted_options[970];
            if (is_array($h)) $h = $h[0];
            $w = is_null($w) ? 0 : floatval($w);
            $h = is_null($h) ? 0 : floatval($h);

            $turn = $posted_options[801];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1290) { // Same day
                if ($sizeId == 1574) $areaPrice = 19.69;
                else if ($sizeId == 1575) $areaPrice = 28.13;
                else if ($sizeId == 1576) $areaPrice = 40.63;
                else $areaPrice = $w * $h * 4.38 / 144;
            } else {
                $areaPrice = 0;
                if ($sizeId == 1577 || $sizeId == 1578) $areaPrice = $w * $h * 2.5 / 144;
            }

            if ($sizeId == 1577) $areaPrice += 15;
            else if ($sizeId == 1578) $areaPrice += 23;

        } else if ($id == 143) { // X-Frame with Banner
            

            $sizeId = $posted_options[978];
            if (is_array($sizeId)) $sizeId = $sizeId[0];

            $w = $posted_options[979];
            if (is_array($w)) $w = $w[0];
            $h = $posted_options[980];
            if (is_array($h)) $h = $h[0];
            $w = is_null($w) ? 0 : floatval($w);
            $h = is_null($h) ? 0 : floatval($h);

            $turn = $posted_options[794];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1280) {
                if ($sizeId == 1594) $areaPrice = 7.01;
                else if ($sizeId == 1595) $areaPrice = 10.01;
                else if ($sizeId == 1596) $areaPrice = 14.46;
                else $areaPrice = $w * $h * 1.5 / 144;
            } else {
                $areaPrice = 0;
                if ($sizeId == 1597 || $sizeId == 1598) $areaPrice = $w * $h * 0.89 / 144;
            }

            if ($sizeId == 1597) $areaPrice += 15;
            else if ($sizeId == 1598) $areaPrice += 23;

        } else if ($id == 144) { // Step and Repeat Banners
            

            $turn = $posted_options[786];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1267) {
                $areaPrice = $area / 144 * 1.513 + 153.8;
            } else  $areaPrice = $area / 144 * 0.89 + 153.8;

        } else if ($id == 145) { // Retractable Stand

            $premiumStand = isset($posted_options[1696]) ? true : false;    
            // if(isset($posted_options[1696])){
            //     $premiumStand = true;
            // }else {
            //     $premiumStand = false;
            // }
            // $matId = $posted_options[1896];
            // if (is_array($matId)) $matId = $matId[0];
            $sizeId = $posted_options[1695];
            if (is_array($sizeId)) $sizeId = $sizeId[0];

            // $isSameDay = $posted_options[1698];
            // if (is_array($isSameDay)) $isSameDay = $isSameDay[0];
            // $isSameDay = ($isSameDay == 2923);
            

            if ($sizeId == 2916){
                $areaPrice = 47; //47;
            }  else if($sizeId == 2917) {
                $areaPrice = 49; //39;
            } else if ($sizeId == 2918) {
                $areaPrice = 52; //52;
            } else if ($sizeId == 2919) {
                $areaPrice = 69; //69; 
            }
            else {
                $areaPrice = 0;
            }
            if ($premiumStand){
                $areaPrice = $areaPrice + 29;
            } 

        } else if ($id == 161) { // Economic Retractable Stand

            $areaPrice = 39;

        } else if ($id == 113) {
            /** Sublimation */  // Custom Sublimation Transfers
            
            $areaPrice = $area / 144 * 0.79;

            $turn = $posted_options[709];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 1190) $has17 = true;

        } else if ($id == 115) {
            /** Contour Cut */  // 3M Controltac (IJ 180C) Contour
            

            $laminationId = $posted_options[716];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[716]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = $area / 144 * (4.4 + $lamPrice) - $lamPrice;

            $turn = $posted_options[717];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 1197) $has17 = true;
            else if ($turn == 1196) $has15 = true;

        } else if ($id == 116) {  // Car Magnets Contour
            

            $laminationId = $posted_options[724];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[724]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = $area / 144 * ($lamPrice + 1.9) - $lamPrice - 1.9;

            $turn = $posted_options[726];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 1207) $has17 = true;
            else if ($turn == 1205) $has15 = true;

        } else if ($id == 117) {  // Floor Graphics – Contour
            

            $areaPrice = $area / 144 * 5.88;

            $turn = $posted_options[735];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 1214) $has17 = true;
            else if ($turn == 1212) $has15 = true;

        } else if ($id == 133) {  // Yard Signs (Coroplast) Contour
            

            $matId = $posted_options[548];
            if (is_array($matId)) $matId = $matId[0];
            $colorId = $posted_options[547];
            if (is_array($colorId)) $colorId = $colorId[0];

            if ($matId == 941) {
                $dieCut = 1.89;
                $color = ($colorId == 939) ? 1.25 : 1.55;
            } else {
                $dieCut = 1.99;
                $color = ($colorId == 939) ? 2.25 : 2.55;
            }

            $areaPrice = $area / 144 * ($color + $dieCut);

            $turn = $posted_options[552];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 951) $has17 = true;
            else if ($turn == 949) $has15 = true;

        } else if ($id == 134) {  // Ultra(Gator) Board Contour
            

            $laminationId = $posted_options[559];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[559]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $colorId = $posted_options[557];
            if (is_array($colorId)) $colorId = $colorId[0];
            if ($colorId == 952) {
                $matOpId = $posted_options[558];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                if ($matOpId == 954) {
                    $matPrice = 3.5;
                } else if ($matOpId == 955) {
                    $matPrice = 3.8;
                } else if ($matOpId == 956) {
                    $matPrice = 5.5;
                } else {
                    $matPrice = 6;
                }

            } else {
                $matOpId = $posted_options[938];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                if ($matOpId == 1534) {
                    $matPrice = 4.9;
                } else if ($matOpId == 1535) {
                    $matPrice = 5.2;
                } else {
                    $matPrice = 7.7;
                }
            }

            $areaPrice = $area / 144 * ($matPrice + 1.99 + $lamPrice) - $lamPrice;

            $turn = $posted_options[562];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 967) $has17 = true;
            else if ($turn == 965) $has15 = true;

        } else if ($id == 135) {  // Styrene Boards Contour
            

            $laminationId = $posted_options[569];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[569]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            // $matId = $posted_options[568];
            // if (is_array($matId)) $matId = $matId[0];
            // $matObj = $opsAr[568]->getValues();
            // $matPrice = $matObj[$matId]->getPrice();
            // $matPrice = is_null($matPrice) ? 0 : $matPrice;

            // code
            $colorId = $posted_options[567];
            if (is_array($colorId)) $colorId = $colorId[0];
            if ($colorId == 968) {
                $matOpId = $posted_options[568];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[568]->getValues();
                $lam2Price = $lamPrice;
            } else {
                $matOpId = $posted_options[1880];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[1880]->getValues();
                $lam2Price = $lamPrice;
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice; 
            // end code
            $areaPrice = $area / 144 * ($matPrice + 1.89 + $lam2Price) - $lamPrice - $matPrice;

            $turn = $posted_options[572];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 982) $has17 = true;
            else if ($turn == 981) $has15 = true;

        } else if ($id == 136) {  // PVC Sintra Board Contour
            
            $laminationId = $posted_options[579];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[579]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $dieCut = 1.89;

            $colorId = $posted_options[577];
            if (is_array($colorId)) $colorId = $colorId[0];

            if ($colorId == 983) {
            	$matId = $posted_options[578];
            	if (is_array($matId)) $matId = $matId[0];

                if ($matId == 985) $matPrice = 2.49;
                else if ($matId == 986) $matPrice = 2.99;
                else if ($matId == 987) {
                    $matPrice = 4.25;
                    $dieCut = 1.99;
                } else {
                    $matPrice = 6.5;
                    $dieCut = 1.99;
                }

            } else {
                $matId = $posted_options[1145];
                if (is_array($matId)) $matId = $matId[0];

                if ($matId == 1784) $matPrice = 3.5;
                else if ($matId == 1785) $matPrice = 3.99;
                else if ($matId == 1786) {
                    $matPrice = 5.95;
                    $dieCut = 1.99;
                } else {
                    $matPrice = 9;
                    $dieCut = 1.99;
                }
            }

            $areaPrice = $area / 144 * ($matPrice + $lamPrice + $dieCut) - $lamPrice;

            $turn = $posted_options[582];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 998) $has17 = true;
            else if ($turn == 996) $has15 = true;

        } else if ($id == 137) {  // Foam Boards- Contour
            

            $laminationId = $posted_options[589];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[589]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $dieCut = 1.99;

            $colorId = $posted_options[587];
            if (is_array($colorId)) $colorId = $colorId[0];

            if ($colorId == 999) $matId = $posted_options[1895];
            else $matId = $posted_options[955];

            if (is_array($matId)) $matId = $matId[0];

            if ($matId == 3279) {
                $matPrice = 2.49;
                $dieCut = 1.89;
            } else if ($matId == 3280) $matPrice = 4;
            else if ($matId == 1556) {
                $matPrice = 3.3;
                $dieCut = 1.89;
            } else {
                $matPrice = 5.4;
            }

            $areaPrice = $area / 144 * ($matPrice + $lamPrice + $dieCut) - $lamPrice;

            $turn = $posted_options[592];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 1013) $has17 = true;
            else if ($turn == 1011) $has15 = true;

        } else if ($id == 138) {  // Wall Vinyl Decals Contour
            

            $laminationId = $posted_options[599];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[599]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $matId = $posted_options[597];
            if (is_array($matId)) $matId = $matId[0];
            $matObj = $opsAr[597]->getValues();
            $matPrice = $matObj[$matId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = $area / 144 * ($matPrice + $lamPrice) - $lamPrice - $matPrice;

            $turn = $posted_options[601];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 1023) $has2 = true;
            else if ($turn == 1021) $has15 = true;

        } else if ($id == 139) {  // Vinyl Stickers (Window Decal) Contour
            
            $laminationId = $posted_options[609];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[609]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $matId = $posted_options[606];
            if (is_array($matId)) $matId = $matId[0];
            $matObj = $opsAr[606]->getValues();
            $matPrice = $matObj[$matId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = $area / 144 * ($matPrice + $lamPrice) - $lamPrice - $matPrice;

            $turn = $posted_options[610];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 1035) $has17 = true;
            else if ($turn == 1034) $has15 = true;

        } else if ($id == 140) {  // Static Clings (Window Clings) Contour
            
            $matId = $posted_options[539];
            if (is_array($matId)) $matId = $matId[0];
            $matObj = $opsAr[539]->getValues();
            $matPrice = $matObj[$matId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = $area / 144 * $matPrice - $matPrice;

            $turn = $posted_options[542];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 938) $has17 = true;
            else if ($turn == 937) $has15 = true;

        }  else if ($id == 166) { // Aluminum Sandwich Board “Dibond” Contour Cut
            
            $laminationId = $posted_options[1942];
            if (is_array($laminationId)) $laminationId = $laminationId[0];
            $lamObj = $opsAr[1942]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $colorOpId = $posted_options[1939];
            if (is_array($colorOpId)) $colorOpId = $colorOpId[0];
            if ($colorOpId == 3346) { // 4:0- Full Color on Front Side Only
                $matOpId = $posted_options[1940];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[1940]->getValues();
                $lam2Price = $lamPrice;
            } else { // 4:4 - Full Color on Both Sides
                $matOpId = $posted_options[1941];
                if (is_array($matOpId)) $matOpId = $matOpId[0];
                $matObj = $opsAr[1941]->getValues();
                $lam2Price = $lamPrice * 2;
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;
          
            $areaPrice = $area / 144 * ($lam2Price + $matPrice) - $lamPrice - $matPrice;
             // add $2
            $areaPrice = $areaPrice;
            $turn = $posted_options[1944];
            if (is_array($turn)) $turn = $turn[0];

            if ($turn == 3359) $has15 = true;
            else if ($turn == 3360) $has175 = true;

        }  else if ($id == 164) { // Aluminum Sheets Contour
            $size = 0;
            $addC = 0;
            $normal = false;

            // Get size id
            $sizeId = $posted_options[1933];
            if (is_array($sizeId)) $sizeId = $sizeId[0];

            $widthVal = $posted_options[1934];
            $widthVal = is_null($widthVal) ? 0 : $widthVal;
            $heightVal = $posted_options[1935];
            $heightVal = is_null($heightVal) ? 0 : $heightVal;
            // set 18 Inches x 12 Inches
            if (($widthVal == 18) && ($heightVal == 12)){
                $sizeId =3341;
            }
            // set 18 Inches x 24 Inches
            if (($widthVal == 18) && ($heightVal == 24)){
                $sizeId =3342;
            }
            // set 24 Inches x 36 Inches
            if (($widthVal == 24) && ($heightVal == 36)){
                $sizeId =3343;
            }
            // Get color id
            $colorId = $posted_options[1923];
            if (is_array($colorId)) $colorId = $colorId[0];

            // Get turnaround
            $turn = $posted_options[1927];
            if (is_array($turn)) $turn = $turn[0];
            if ($turn == 3334) $has15 = true; // next day: 1.5 --- 2 day: 1
            else if ($turn == 3335) $has175 = true; // same day: 1.75

            // Get Lam id
            $lamId = $posted_options[1926];
            if (is_array($lamId)) $lamId = $lamId[0];
            $lamPrice = 0;
             if ($sizeId == 3341) {
                if ($colorId == 3321) { // 4:0- Full Color on Front Side Only
                    $size = 7.5;$addC = 0;
                    if ($lamId == 3330) $lamPrice = 1.2;
                    else if ($lamId == 3331) $lamPrice = 1.21;
                    else if ($lamId == 3332) $lamPrice = 3.75;
                } else { // 4:4 - Full Color on Both Sides
                    $size = 11.25;$addC = 0;
                    if ($lamId == 3330) $lamPrice = 2.4;
                    else if ($lamId == 3331) $lamPrice = 2.41;
                    else if ($lamId == 3332) $lamPrice = 7.5;
                }

            } else if ($sizeId == 3342) {
                if ($colorId == 3321) { // 4:0- Full Color on Front Side Only
                    $size = 15;$addC = 0;
                    if ($lamId == 3330) $lamPrice = 2.4;
                    else if ($lamId == 3331) $lamPrice = 2.41;
                    else if ($lamId == 3332) $lamPrice = 7.5;
                } else { // 4:4 - Full Color on Both Sides
                    $size = 22.5;$addC = 0;
                    if ($lamId == 3330) $lamPrice = 4.8;
                    else if ($lamId == 3331) $lamPrice = 4.81;
                    else if ($lamId == 3332) $lamPrice = 15;
                }

            } else if ($sizeId == 3343) {
                if ($colorId == 3321) { // 4:0- Full Color on Front Side Only
                    $size = 30;$addC = 0;
                    if ($lamId == 3330) $lamPrice = 4.8;
                    else if ($lamId == 3331) $lamPrice = 4.81;
                    else if ($lamId == 3332) $lamPrice = 15;
                } else { // 4:4 - Full Color on Both Sides
                    $size = 45;$addC = 0;
                    if ($lamId == 3330) $lamPrice = 9.6;
                    else if ($lamId == 3331) $lamPrice = 9.61;
                    else if ($lamId == 3332) $lamPrice = 30;
                }

            } else { // Custom

                $normal = true;

                $widthVal = $posted_options[1934];
                $widthVal = is_null($widthVal) ? 0 : $widthVal;
                $heightVal = $posted_options[1935];
                $heightVal = is_null($heightVal) ? 0 : $heightVal;

                if ($lamId == 3330) $lamPrice = 0.8;
                else if ($lamId == 3331) $lamPrice = 0.81;
                else if ($lamId == 3332) $lamPrice = 2.5;

                $lamPrice = ($colorId == 3321) ? $lamPrice + 6 : $lamPrice * 2 + 10;
                if ($turn == 3335 && $colorId != 3321) $lamPrice -= 4;

               // $areaPrice = $widthVal * $heightVal / 144 * $lamPrice;
                $areaPrice = $widthVal * $heightVal / 144 *9;
            }
                
            // add $2
         //   $areaPrice =$areaPrice +2;
               
            $size=$size;
            // Set price
            if (!$normal) $areaPrice = $size + $lamPrice;

        } else if ($id == 118) {
            /** Hardware */ // Easel Backs

        } else if ($id == 119) {  // Retractable Stand (stand ONLY)
			

            $premiumStandonly = isset($posted_options[1071]) ? true : false;    

            $sizeId = $posted_options[699];
            if (is_array($sizeId)) $sizeId = $sizeId[0];
                         
            if ($premiumStandonly){
                $areaPrice = $areaPrice + 29;
            } 		
		
		

        } else if ($id == 120) {  // Outdoor Stand (for Banner)

        } else if ($id == 121) {  // Step and Repeat Stand

        } else if ($id == 122) {  // Suction Cups

        } else if ($id == 124) {  // H-Stakes

        } else if ($id == 154) {  // X - Stand

        }


        // Default Calc
        $final_price = $quoteItem->getProduct()->getFinalPrice(); //base-price + selected options
        //$this->logger->debug('$final_price: ' . $final_price);
        // if ($notFixAreaPrice)  $areaPrice = $this->_calculatePrice($_product, $posted_options); // Default

        $total = $areaPrice + $final_price;
        //$this->logger->debug('$areaPrice: ' . $areaPrice);

        if ($has15) $total *= 1.5;
        else if ($has17) $total *= 1.7;
        else if ($has175) $total *= 1.75;
        else if ($has2) $total *= 2;
        else if ($has34) $total *= 3.4;

        // Start Discount
        $item = ($quoteItem->getParentItem() ? $quoteItem->getParentItem() : $quoteItem);
        $qty = $item->getQty();
        //$this->logger->debug('$total before discount: ' . $total);

        // Get Discount
        if ($id == 145 || $id == 161) {
                if($qty <= 5){
                    $percentRate = 0;
                } else if($qty >= 6 && $qty <= 10){
                    $percentRate = 1;
                } else if($qty >= 11 && $qty <= 20){
                    $percentRate = 2;
                } else if($qty >= 21 && $qty <= 30){
                    $percentRate = 3;
                } else if($qty >= 31 && $qty <= 40){
                    $percentRate = 4;
                } else if($qty >= 41 && $qty <= 50){
                    $percentRate = 5;
                } else if($qty >= 51 && $qty <= 60){
                    $percentRate = 6;
                } else if($qty >= 61 && $qty <= 70){
                    $percentRate = 7;
                } else if($qty >= 71 && $qty <= 80){
                    $percentRate = 8;
                } else if($qty >= 81 && $qty <= 90){
                    $percentRate = 9;
                } else if($qty >= 91 && $qty <= 100){
                    $percentRate = 10;
                } else if($qty >= 101 && $qty <= 110){
                    $percentRate = 10;
                } else if($qty >= 111 && $qty <= 120){
                    $percentRate = 10;
                } else if($qty >= 121 && $qty <= 130){
                    $percentRate = 10;
                } else if($qty >= 131 && $qty <= 140){
                    $percentRate = 10;
                } else if($qty >= 141 && $qty <= 150){
                    $percentRate = 10;
                } else if($qty >= 151 && $qty <= 160){
                    $percentRate = 10;
                } else if($qty >= 161 && $qty <= 170){
                    $percentRate = 10;
                } else if($qty >= 171 && $qty <= 180){
                    $percentRate = 10;
                } else if($qty >= 181){
                    $percentRate = 10;
                }

            } else if ($id == 111 || $id == 112 || $id == 120) {
                if($qty <= 10){
                    $percentRate = 0;
                } else if($qty >= 11 && $qty <= 20){
                    $percentRate = 5;
                } else if($qty >= 21 && $qty <= 30){
                    $percentRate = 10;
                } else if($qty >= 31 && $qty <= 40){
                    $percentRate = 15;
                } else if($qty >= 41){
                    $percentRate = 20;
			
				}				
				
				
				
				
            } else{
                $percentRate = $hasDiscount ? $this->getDiscountRate(($area * $qty), $_product) : 0;
            }
        // $this->logger->debug("Discount percentRate : ". $percentRate);
        // $this->logger->debug("total : ". $total);
   
        $total = $total - $total * $percentRate / 100;
        if ($qty * $total < 8) $total = $this->checkMinPriceForTotal($qty, 8.0);
        // End Discount

        // Check min price for total price
        //        minimum 5$: http://tradebanner.com/shop/bumper-stickers/
        //        minimum 5$: http://tradebanner.com/shop/3m-controltac-ij-180c-contour/
        //        minimum 5$: http://tradebanner.com/shop/floor-graphics-contour-cut/
        //        minimum 5$: http://tradebanner.com/shop/wall-vinyl-decals-contour-cut/
        //        minimum 5$: http://tradebanner.com/shop/styrene-boards-contour-cut/
        //        minimum 5$: http://tradebanner.com/shop/easel-back/
        //
        //        minimum 10$: http://tradebanner.com/shop/custom-sublimation-transfers/
        //        minimum 10$; http://tradebanner.com/shop/car-magnets-contour-cut/
        //        minimum: 10$: http://tradebanner.com/shop/static-clings-contour-cut/
        //        minimum 10$ : http://tradebanner.com/shop/contour-cut-vinyl-stickers/
        //        minimum 10$: http://tradebanner.com/shop/foam-boards-contour-cut/
        //        minimum 10$: http://tradebanner.com/shop/pvc-sintra-board-countour-copy/
        //        minimum 10$: http://tradebanner.com/shop/ultragator-board-countour/
        //        minimum 10$ : http://tradebanner.com/shop/yard-signs-coroplast-contour-cut/

        // Except: min 8$ for unit price
        //  Banner category: 90, 91, 92, 93, 94, 95, 96, 97, 98, 99

        // minimum 5$ for unit price: http://dev.tradebanner.com/sample-kit.html 109

        $banner = [90, 91, 92, 93, 94, 95, 96, 97, 98, 99];
        $min5 = [102, 115, 117, 138, 135, 118];
        $min10 = [113, 116, 140, 139, 137, 136, 134, 133];

        if ($id == 109) $total = 5; // sample-kit
        else if ( ($qty * $total) <= 10 && in_array($id, $min10) ) $total = $this->checkMinPriceForTotal($qty, 10.0);
        else if ( ($qty * $total) <= 5 && in_array($id, $min5) ) $total = $this->checkMinPriceForTotal($qty, 5.0);
        else if ( $total <= 8 && in_array($id, $banner) ) $total = 8;
        else if ( ($qty * $total) <= 8 ) $total = $this->checkMinPriceForTotal($qty, 8.0);


        // Update Price
        $item->setCustomPrice($total);
        $item->setOriginalCustomPrice($total);
        $item->getProduct()->setIsSuperMode(true);

        //$this->logger->debug('$total final: ' . $total);
        //$this->logger->debug("================== End debug kong ====================");
    }

    /**
     * Get Discount Rate
     * @param $area
     * @param $_product
     * @return float
     */
    public function getDiscountRate($area, $_product)
    {
        $config = $this->_pcHelper->getProductPricingRule($_product);
        $rate = 0;
        if (isset($config['size']) && isset($config['discount'])) {
            $c_size = count($config['size']);
            //$c_discount = count($config['discount']);

            foreach ($config['size'] as $i => $size) {
                if ($i < ($c_size - 1) && intval($size) <= $area && $area < intval($config['size'][$i + 1])) {
                    if (isset($config['discount'][$i])) {
                        $rate = $config['discount'][$i];
                        break;
                    }
                } else if ($i == ($c_size - 1) && intval($size) <= $area) {
                    if (isset($config['discount'][$i])) {
                        $rate = $config['discount'][$i];
                        break;
                    }
                }
            }
        }
        return floatval($rate);
    }


    public function checkEightUsd($qty)
    {
        $rs = 8.0 / $qty;
        $digit = strlen(substr(strrchr(strval($rs), "."), 1));

        if ($digit > 2) {
            $rs = intval($rs * 100);
            $rs = $rs / 100 + 0.01;
        }
        return $rs;
    }

    /**
     * @param $qty
     * @param int $usd
     * @return float|int
     */
    public function checkMinPriceForTotal($qty, $usd = 8)
    {
        $rs = $usd / $qty;
        $digit = strlen(substr(strrchr(strval($rs), "."), 1));

        if ($digit > 2) {
            $rs = intval($rs * 100);
            $rs = $rs / 100 + 0.01;
        }
        return $rs;
    }


    protected function _calculatePrice($_product, $posted_options)
    {
        $unitPrice = $_product->getPriceUnitArea();
        $area = $this->_calculateArea($_product, $posted_options);

        $discount = 0;
        // $discount = $this->_calculateDiscount($area, $unitPrice, $_product);

        $price = ($unitPrice * $area) - $discount;

        return $price;
    }


    protected function _calculateArea($_product, $posted_options)
    {
        $customOptions = $_product->getOptions();
        $fieldOptions = $this->_pcHelper->getFieldOptions($_product);
        $area = 1;

        foreach ($customOptions as $option) {
            if (isset($fieldOptions[$option->getTitle()])) {
                $posted_val = $posted_options[$option->getId()];
                $area = $area * (float)$posted_val;
            }
        }

        $inputUnit = $this->_pcHelper->getInputUnitLabel($_product);
        $outputUnit = $this->_pcHelper->getOutputUnitLabel($_product);
        $unitConversion = $this->_pcHelper->unitConversion($inputUnit, $outputUnit);

        $area = $area * (float)$unitConversion;

        return $area;
    }

    protected function _calculateDiscount($area, $unitPrice, $_product)
    {
        $rules = $this->_pcHelper->getProductPricingRule($_product);
        $discount = 0;

        if ($area < $rules['size']['min_limit']) {
            $discount = 0;
        } else if ($area >= $rules['size']['min_limit'] && $area < $rules['size']['max_limit']) {
            if ($rules['type'] == 'percent') {
                $discount = (($area * $unitPrice) * ($rules['discount']['min_limit'] / 100));
            } else {
                $discount = $rules['discount']['min_limit'];
            }
        } else if ($area >= $rules['size']['max_limit']) {
            if ($rules['type'] == 'percent') {
                $discount = ($area * $unitPrice) * ($rules['discount']['max_limit'] / 100);
            } else {
                $discount = ($rules['discount']['max_limit']);
            }
        }

        return $discount;
    }


}
