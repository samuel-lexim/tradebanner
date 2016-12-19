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
        $this->logger->debug("================== Start debug kong ====================");
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $_product = $observer->getProduct();
        $customOptions = $_product->getOptions();
        $opsAr = [];

        $totalOp = 0;
        $id = $_product->getId();
        $unitPrice = $_product->getPriceUnitArea();


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
        $posted_options = $params['options']; // { 'select id' => 'option id' }
        // {"318":"44","319":"66","316":"594","317":"595","320":"598","680":"1136","321":"605","322":"606","323":"609","324":"615","325":"618","326":"22","740":"asdsad"}


        // Calc price foreach products - Flags
        $has15 = false;
        $has17 = false;
        $has175 = false;
        $has2 = false;
        $has34 = false;
        $notFixAreaPrice = true;
        $areaPrice = 0;
        $area = $this->_calculateArea($_product, $posted_options);

        /** Banner */
        if ($id == 90) { // 13oz Vinyl Banner
            if ($posted_options[303] == 1314) $has17 = true;

        } else if ($id == 91) { // 14oz Vinyl Banner
            if ($posted_options[314] == 593) $has17 = true;

        } else if ($id == 92) { // 16 oz Blockout Banner
            if ($posted_options[325] == 618) $has17 = true;

        } else if ($id == 93) { // Backlit Banner
            if ($posted_options[334] == 633) $has17 = true;

        } else if ($id == 94) { // Double Sided Banner (Super Smooth)
            $notFixAreaPrice = false;
            $matOpId = $posted_options[336];
            $matObj = $customOptions[336]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($unitPrice * $area * $matPrice) - $matPrice;
            if ($posted_options[343] == 654) $has17 = true;

        } else if ($id == 95) { // Fabric Banner- Premium
            if ($posted_options[353] == 675) $has17 = true;

        } else if ($id == 96) { // Mesh Banner
            if ($posted_options[395] == 764) $has17 = true;

//        } else if ($id == 97) { // Premium Coated Banner
//            if ($posted_options[385] == 744) $has17 = true;
//
//        } else if ($id == 97) { // Premium Coated Banner
//            if ($posted_options[385] == 744) $has17 = true;

        } else if ($id == 98) { // Super Smooth Banner
            $notFixAreaPrice = false;
            $matOpId = $posted_options[365];
            $matObj = $customOptions[365]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($unitPrice * $area * $matPrice) - $matPrice;
            if ($posted_options[374] == 720) $has17 = true;

        } else if ($id == 99) { // Fabric Banner- Deluxe
            if ($posted_options[362] == 694) $has17 = true;

        } else if ($id == 100) {
            /** Adhesives */ // 3M Controltac (IJ 180C)
            $notFixAreaPrice = false;
            $laminationId = $posted_options[402];
            $lamObj = $customOptions[402]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($unitPrice * $area * (2.5 + $lamPrice)) - $lamPrice;

            if ($posted_options[403] == 770) $has17 = true;

        } else if ($id == 101) { // 3M IJ35 Adhesive Vinyl
            $notFixAreaPrice = false;
            $laminationId = $posted_options[410];
            $lamObj = $customOptions[410]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $matOpId = $posted_options[408];
            $matObj = $customOptions[408]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($unitPrice * $area * ($matPrice + $lamPrice)) - $lamPrice - $matPrice;
            if ($posted_options[411] == 778) $has17 = true;

        } else if ($id == 102) { // Bumper Stickers
            if ($posted_options[418] == 788) $has17 = true;

        } else if ($id == 123) { // Floor Graphics
            $notFixAreaPrice = false;
            $laminationId = $posted_options[507];
            $lamObj = $customOptions[507]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $matOpId = $posted_options[505];
            $matObj = $customOptions[505]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($unitPrice * $area * ($matPrice + $lamPrice)) - $lamPrice - $matPrice;
            if ($posted_options[508] == 901) $has17 = true;

        } else if ($id == 126) { // Perforated Stickers (One-Way Vision)
            $notFixAreaPrice = false;
            $matOpId = $posted_options[498];
            $matObj = $customOptions[498]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($unitPrice * $area * $matPrice) - $matPrice;
            if ($posted_options[500] == 896) $has17 = true;

        } else if ($id == 127) { // Static Clings (Window Clings)
            $notFixAreaPrice = false;
            $matOpId = $posted_options[491];
            $matObj = $customOptions[491]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($unitPrice * $area * $matPrice) - $matPrice;
            if ($posted_options[493] == 892) $has17 = true;

        } else if ($id == 128) { // Vinyl Stickers (Window Decal)
            $notFixAreaPrice = false;
            $matOpId = $posted_options[483];
            $matObj = $customOptions[483]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $laminationId = $posted_options[485];
            $lamObj = $customOptions[485]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($unitPrice * $area * ($matPrice + $lamPrice)) - $matPrice - $lamPrice;

            if ($posted_options[486] == 887) $has17 = true;

        } else if ($id == 129) { // Wall Vinyl Decals
            $notFixAreaPrice = false;
            $matOpId = $posted_options[475];
            $matObj = $customOptions[475]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $laminationId = $posted_options[477];
            $lamObj = $customOptions[477]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($unitPrice * $area * ($matPrice + $lamPrice)) - $matPrice - $lamPrice;

            if ($posted_options[478] == 877) $has2 = true;

        } else if ($id == 103) {
            /** Digital Prints */ // Backlit Film
            $notFixAreaPrice = false;
            $laminationId = $posted_options[426];
            $lamObj = $customOptions[426]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($unitPrice * $area * (3.5 + $lamPrice)) - $lamPrice;

            if ($posted_options[427] == 796) $has17 = true;

        } else if ($id == 104) { // Canvas Roll
            $notFixAreaPrice = false;
            $matOpId = $posted_options[430];
            $matObj = $customOptions[430]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = ($unitPrice * $area * $matPrice) - $matPrice;
            if ($posted_options[434] == 801) $has17 = true;

        } else if ($id == 130) { // Polypropylene (PET)
            $notFixAreaPrice = false;
            $laminationId = $posted_options[516];
            $lamObj = $customOptions[516]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($unitPrice * $area * (2.5 + $lamPrice)) - $lamPrice;

            if ($posted_options[515] == 905) $has17 = true;

        } else if ($id == 131) { // Digital Poster
            $notFixAreaPrice = false;
            $laminationId = $posted_options[524];
            $lamObj = $customOptions[524]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = ($unitPrice * $area * $lamPrice) - $lamPrice;

            if ($posted_options[525] == 921) $has17 = true;

        } else if ($id == 132) { // Car Magnets
            $notFixAreaPrice = false;
            $laminationId = $posted_options[532];
            $lamObj = $customOptions[532]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $roundId = $posted_options[533];
            $roundObj = $customOptions[533]->getValues();
            $roundPrice = $roundObj[$roundId]->getPrice();
            $roundPrice = is_null($roundPrice) ? 0 : $roundPrice;

            $areaPrice = ($unitPrice * $area * ($lamPrice + $roundPrice)) - $lamPrice - $roundPrice;

            if ($posted_options[534] == 931) $has17 = true;

        } else if ($id == 155) { // Framed Canvas
            $notFixAreaPrice = false;

            $widthVal = $posted_options[811];
            $widthVal = is_null($widthVal) ? 0 : $widthVal;

            $heightVal = $posted_options[812];
            $heightVal = is_null($heightVal) ? 0 : $heightVal;

            $goodWidth = ($widthVal == 12 || $widthVal == 18 || $widthVal == 24 || $widthVal == 30 || $widthVal == 36 || $widthVal == 48);
            $goodHeight = ($heightVal == 12 || $heightVal == 18 || $heightVal == 24 || $heightVal == 30 || $heightVal == 36 || $heightVal == 48);

            if ($goodWidth && $goodHeight) {
                $areaPrice = $area * 8 / 144;
                if ($posted_options[815] == 1305) $areaPrice *= 2;
            } else $areaPrice = $area * 9 / 144;

            if ($posted_options[815] == 1303) $has15 = true; // next day: 1.5 --- 2 day: 1
            else if ($posted_options[815] == 1305) $has17 = true; // same day: 1.7

        } else if ($id == 106) {
            /** SIGNS & BOARDS */ // Acrylic Boards
            $notFixAreaPrice = false;

            $laminationId = $posted_options[448];
            $lamObj = $customOptions[448]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $matOpId = $posted_options[447];
            $matObj = $customOptions[447]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $drillOpId = $posted_options[449];
            $drillObj = $customOptions[449]->getValues();
            $drillPrice = $drillObj[$drillOpId]->getPrice();
            $drillPrice = is_null($drillPrice) ? 0 : $drillPrice;

            $areaPrice = ($unitPrice * $area * ($lamPrice + $matPrice)) - $lamPrice - $matPrice;

            if ($posted_options[451] == 834) $areaPrice = ($unitPrice * $area * ($lamPrice + $matPrice) + $drillPrice) * 1.7 - $lamPrice - $matPrice - $drillPrice;

        } else if ($id == 107) { // Aluminum Sandwich Board “Dibond”
            $notFixAreaPrice = false;

            $laminationId = $posted_options[458];
            $lamObj = $customOptions[458]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $colorOpId = $posted_options[456];
            if ($colorOpId == 835) { // 4:0- Full Color on Front Side Only
                $matOpId = $posted_options[457];
                $matObj = $customOptions[457]->getValues();
                $lam2Price = $lamPrice;
            } else { // 4:4 - Full Color on Both Sides
                $matOpId = $posted_options[870];
                $matObj = $customOptions[870]->getValues();
                $lam2Price = $lamPrice * 2;
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = $area / 144 * ($lam2Price + $matPrice) - $lamPrice - $matPrice;

            if ($posted_options[460] == 846) $has15 = true;
            else if ($posted_options[460] == 848) $has17 = true;

        } else if ($id == 108) { // Aluminum Sheets
            $notFixAreaPrice = false;

            $widthVal = $posted_options[818];
            $widthVal = is_null($widthVal) ? 0 : $widthVal;
            $heightVal = $posted_options[819];
            $heightVal = is_null($heightVal) ? 0 : $heightVal;

            $lamId = $posted_options[468];

            $colorId = $posted_options[464];

            $size = 0;
            $normal = false;
            if ($widthVal == 18 && $heightVal == 12) {
                if ($colorId == 850) { // 4:0- Full Color on Front Side Only
                    $size = 7.5;
                    if ($lamId == 863) $lamPrice = 1.2;
                    else if ($lamId == 864) $lamPrice = 1.21;
                    else if ($lamId == 865) $lamPrice = 3.75;
                    else $lamPrice = 0;
                } else { // 4:4 - Full Color on Both Sides
                    $size = 11.25;
                    if ($lamId == 863) $lamPrice = 2.4;
                    else if ($lamId == 864) $lamPrice = 2.41;
                    else if ($lamId == 865) $lamPrice = 7.5;
                    else $lamPrice = 0;
                }
            } else if ($widthVal == 18 && $heightVal == 24) {
                if ($colorId == 850) { // 4:0- Full Color on Front Side Only
                    $size = 15;
                    if ($lamId == 863) $lamPrice = 2.4;
                    else if ($lamId == 864) $lamPrice = 2.41;
                    else if ($lamId == 865) $lamPrice = 7.5;
                    else $lamPrice = 0;
                } else { // 4:4 - Full Color on Both Sides
                    $size = 22.5;
                    if ($lamId == 863) $lamPrice = 4.8;
                    else if ($lamId == 864) $lamPrice = 4.81;
                    else if ($lamId == 865) $lamPrice = 15;
                    else $lamPrice = 0;
                }
            } else if ($widthVal == 24 && $heightVal == 36) {
                if ($colorId == 850) { // 4:0- Full Color on Front Side Only
                    $size = 30;
                    if ($lamId == 863) $lamPrice = 4.8;
                    else if ($lamId == 864) $lamPrice = 4.81;
                    else if ($lamId == 865) $lamPrice = 15;
                    else $lamPrice = 0;
                } else { // 4:4 - Full Color on Both Sides
                    $size = 45;
                    if ($lamId == 863) $lamPrice = 9.6;
                    else if ($lamId == 864) $lamPrice = 9.61;
                    else if ($lamId == 865) $lamPrice = 30;
                    else $lamPrice = 0;
                }
            } else {
                $normal = true;
                if ($lamId == 863) $lamPrice = 0.8;
                else if ($lamId == 864) $lamPrice = 0.81;
                else if ($lamId == 865) $lamPrice = 2.5;
                else $lamPrice = 0;
            }

            if ($normal) {
                $areaPrice = $unitPrice * $area * (6 + $lamPrice);
            } else {
                $areaPrice = $size + $lamPrice;
            }

            if ($posted_options[469] == 867) $has15 = true; // next day: 1.5 --- 2 day: 1
            else if ($posted_options[469] == 868) $has175 = true; // same day: 1.75

        } else if ($id == 146) { // Arrow Spinner Signs
            $notFixAreaPrice = false;
            $colorId = $posted_options[744];
            $sizeId = $posted_options[973];
            $lamId = $posted_options[974];

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
            if ($posted_options[747] == 1226) $has17 = true;

        } else if ($id == 147) { // Yard Signs (Coroplast)
            $notFixAreaPrice = false;

            $matVal = $posted_options[615];

            if ($matVal == 1036) {
                $colorId = $posted_options[616];
                $colorObj = $customOptions[616]->getValues();
            } else {
                $colorId = $posted_options[891];
                $colorObj = $customOptions[891]->getValues();
            }

            $colorPrice = $colorObj[$colorId]->getPrice();
            $colorPrice = is_null($colorPrice) ? 1 : $colorPrice;

            $areaPrice = ($unitPrice * $area * $colorPrice) - $colorPrice;

            if ($posted_options[619] == 1048) $has17 = true;

        } else if ($id == 148) { // Ultra(Gator) Board
            $notFixAreaPrice = false;

            $laminationId = $posted_options[626];
            $lamObj = $customOptions[626]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $groId = $posted_options[627];
            $groObj = $customOptions[627]->getValues();
            $groPrice = $groObj[$groId]->getPrice();
            $groPrice = is_null($groPrice) ? 0 : $groPrice;

            $colorId = $posted_options[624];
            if ($colorId == 1049) {
                $matOpId = $posted_options[625];
                $matObj = $customOptions[625]->getValues();
                $matPrice = $matObj[$matOpId]->getPrice();
                $matPrice = is_null($matPrice) ? 0 : $matPrice;
                $areaPrice = ($unitPrice * $area * ($lamPrice + $matPrice)) - $lamPrice - $matPrice;

            } else {
                $matOpId = $posted_options[888];
                $matObj = $customOptions[888]->getValues();
                $matPrice = $matObj[$matOpId]->getPrice();
                $matPrice = is_null($matPrice) ? 0 : $matPrice;
                $areaPrice = ($unitPrice * $area * ($lamPrice * 2 + $matPrice)) - $lamPrice - $matPrice;
            }

            if ($matOpId == 1053 || $matOpId == 1452) $areaPrice -= $groPrice;


            if ($posted_options[628] == 1064) $has17 = true;

        } else if ($id == 149) { // Styrene Boards
            $notFixAreaPrice = false;

            $laminationId = $posted_options[635];
            $lamObj = $customOptions[635]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $groId = $posted_options[636];
            $groObj = $customOptions[636]->getValues();
            $groPrice = $groObj[$groId]->getPrice();
            $groPrice = is_null($groPrice) ? 0 : $groPrice;

            $matOpId = $posted_options[634];
            $matObj = $customOptions[634]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            if ($posted_options[638] == 1079) {
                $areaPrice = ($unitPrice * $area * ($lamPrice + $matPrice) + $groPrice) * 1.7 - $groPrice - $lamPrice - $matPrice;
            } else {
                $areaPrice = ($unitPrice * $area * ($lamPrice + $matPrice)) - $lamPrice - $matPrice;
            }

        } else if ($id == 150) { // PVC Sintra Board
            $notFixAreaPrice = false;

            $laminationId = $posted_options[645];
            $lamObj = $customOptions[645]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $groId = $posted_options[646];
            $groObj = $customOptions[646]->getValues();
            $groPrice = $groObj[$groId]->getPrice();
            $groPrice = is_null($groPrice) ? 0 : $groPrice;

            $colorId = $posted_options[643];
            if ($colorId == 1080) {
                $matOpId = $posted_options[644];
                $matObj = $customOptions[644]->getValues();
                $lam2Price = $lamPrice;
            } else {
                $matOpId = $posted_options[885];
                $matObj = $customOptions[885]->getValues();
                $lam2Price = $lamPrice * 2;
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            if ($posted_options[648] == 1097) {
                $areaPrice = ($area / 144 * ($lam2Price + $matPrice) + $groPrice) * 1.7 - $groPrice - $lamPrice - $matPrice;
            } else {
                $areaPrice = ($area / 144 * ($lam2Price + $matPrice)) - $lamPrice - $matPrice;
            }

        } else if ($id == 151) { // Foam Boards
            $notFixAreaPrice = false;

            $laminationId = $posted_options[655];
            $lamObj = $customOptions[655]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $colorId = $posted_options[653];
            if ($colorId == 1098) {
                $matOpId = $posted_options[654];
                $matObj = $customOptions[654]->getValues();
            } else {
                $matOpId = $posted_options[883];
                $matObj = $customOptions[883]->getValues();
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            if ($colorId == 1098) {
                $areaPrice = $area / 144 * ($lamPrice + $matPrice) - $lamPrice - $matPrice;
            } else {
                $areaPrice = $area / 144 * ($lamPrice * 2 + $matPrice) - $lamPrice - $matPrice;
            }

            if ($posted_options[657] == 1112) $has17 = true;

        } else if ($id == 152) { // Eagle Board (Eco Board)
            $notFixAreaPrice = false;

            $laminationId = $posted_options[664];
            $lamObj = $customOptions[664]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $colorId = $posted_options[662];
            if ($colorId == 1113) {
                $matOpId = $posted_options[663];
                $matObj = $customOptions[663]->getValues();
            } else {
                $matOpId = $posted_options[881];
                $matObj = $customOptions[881]->getValues();
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            if ($colorId == 1113) {
                $areaPrice = $area / 144 * ($lamPrice + $matPrice) - $lamPrice - $matPrice;
            } else {
                $areaPrice = $area / 144 * ($lamPrice * 2 + $matPrice) - $lamPrice - $matPrice;
            }

            if ($posted_options[665] == 1121) $has17 = true;

        } else if ($id == 153) { // Converd Coated Board
            $notFixAreaPrice = false;

            $laminationId = $posted_options[672];
            $lamObj = $customOptions[672]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $colorId = $posted_options[670];
            if ($colorId == 1122) {
                $matOpId = $posted_options[671];
                $matObj = $customOptions[671]->getValues();
            } else {
                $matOpId = $posted_options[879];
                $matObj = $customOptions[879]->getValues();
            }
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            if ($colorId == 1122) {
                $areaPrice = $area / 144 * ($lamPrice + $matPrice) - $lamPrice - $matPrice;
            } else {
                $areaPrice = $area / 144 * ($lamPrice * 2 + $matPrice) - $lamPrice - $matPrice;
            }

            if ($posted_options[674] == 1134) $has17 = true;

        } else if ($id == 110) {
            /** Banner stands */  // Mini Banner Stands
            $notFixAreaPrice = false;
            $areaPrice = 0;
            if ($posted_options[756] == 1237) $has17 = true;

        } else if ($id == 111) { // Outdoor Banner Stand
            $notFixAreaPrice = false;
            $areaPrice = 0;
            if ($posted_options[765] == 1247) $has17 = true;

        } else if ($id == 112) { // Outdoor Double Banner Stand
            $notFixAreaPrice = false;

            $matOpId = $posted_options[770];
            $matObj = $customOptions[770]->getValues();
            $matPrice = $matObj[$matOpId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            if ($posted_options[774] == 1257) { // Same day
                $areaPrice = $matPrice * 1.7 - 80 - $matPrice;
            } else {
                $areaPrice = 0;
            }

        } else if ($id == 141) { // X-Frame with Supersmooth
            $notFixAreaPrice = false;

            $sizeId = $posted_options[963];

            $w = $posted_options[965];
            $h = $posted_options[966];
            $w = is_null($w) ? 0 : floatval($w);
            $h = is_null($h) ? 0 : floatval($h);

            if ($posted_options[808] == 1300) { // Same day
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
            $notFixAreaPrice = false;

            $sizeId = $posted_options[964];

            $w = $posted_options[969];
            $h = $posted_options[970];
            $w = is_null($w) ? 0 : floatval($w);
            $h = is_null($h) ? 0 : floatval($h);

            if ($posted_options[801] == 1290) { // Same day
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
            $notFixAreaPrice = false;

            $sizeId = $posted_options[978];

            $w = $posted_options[979];
            $h = $posted_options[980];
            $w = is_null($w) ? 0 : floatval($w);
            $h = is_null($h) ? 0 : floatval($h);

            if ($posted_options[794] == 1280) {
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
            $notFixAreaPrice = false;

            if ($posted_options[786] == 1267) {
                $areaPrice = $area / 144 * 1.513 + 153.8;
            } else  $areaPrice = $area / 144 * 0.89 + 153.8;

        } else if ($id == 145) { // Retractable Stand
            $notFixAreaPrice = false;

            $matId = $posted_options[976];
            $sizeId = $posted_options[975];
            $isSameDay = ($posted_options[698] == 1173);

            if ($matId == 1590) {
                if ($sizeId == 1586) $areaPrice = ($isSameDay) ? 92.5 : 75;
                else if ($sizeId == 1587) $areaPrice = ($isSameDay) ? 111 : 85;
                else if ($sizeId == 1588) $areaPrice = ($isSameDay) ? 122 : 95;
                else $areaPrice = ($isSameDay) ? 151 : 115;
            } else if ($matId == 1591) {
                if ($sizeId == 1586) $areaPrice = ($isSameDay) ? 83 : 69;
                else if ($sizeId == 1587) $areaPrice = ($isSameDay) ? 98.5 : 79;
                else if ($sizeId == 1588) $areaPrice = ($isSameDay) ? 109.5 : 89;
                else $areaPrice = ($isSameDay) ? 137.5 : 110;
            } else {
                if ($sizeId == 1586) $areaPrice = ($isSameDay) ? 104.5 : 82;
                else if ($sizeId == 1587) $areaPrice = ($isSameDay) ? 127.5 : 95;
                else if ($sizeId == 1588) $areaPrice = ($isSameDay) ? 139.5 : 105;
                else $areaPrice = ($isSameDay) ? 166 : 120;
            }

        } else if ($id == 113) {
            /** Sublimation */  // Custom Sublimation Transfers
            $notFixAreaPrice = false;
            $areaPrice = $area / 144 * 0.79;
            if ($posted_options[709] == 1190) $has17 = true;

        } else if ($id == 115) {
            /** Contour Cut */  // 3M Controltac (IJ 180C) Contour
            $notFixAreaPrice = false;

            $laminationId = $posted_options[716];
            $lamObj = $customOptions[716]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = $area / 144 * (4.4 + $lamPrice) - $lamPrice;

            if ($posted_options[717] == 1197) $has17 = true;
            else if ($posted_options[717] == 1196) $has15 = true;

        } else if ($id == 116) {  // Car Magnets Contour
            $notFixAreaPrice = false;

            $laminationId = $posted_options[724];
            $lamObj = $customOptions[724]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $areaPrice = $area / 144 * ($lamPrice + 1.9) - $lamPrice - 1.9;

            if ($posted_options[726] == 1207) $has17 = true;
            else if ($posted_options[726] == 1205) $has15 = true;

        } else if ($id == 117) {  // Floor Graphics – Contour
            $notFixAreaPrice = false;

            $areaPrice = $area / 144 * 5.88;

            if ($posted_options[735] == 1214) $has17 = true;
            else if ($posted_options[735] == 1212) $has15 = true;

        } else if ($id == 133) {  // Yard Signs (Coroplast) Contour
            $notFixAreaPrice = false;

            $matId = $posted_options[548];
            $colorId = $posted_options[547];

            if ($matId == 941) {
                $dieCut = 1.89;
                $color = ($colorId == 939) ? 1.25 : 1.55;
            } else {
                $dieCut = 1.99;
                $color = ($colorId == 939) ? 2.25 : 2.55;
            }

            $areaPrice = $area / 144 * ($color + $dieCut);

            if ($posted_options[552] == 951) $has17 = true;
            else if ($posted_options[552] == 949) $has15 = true;

        } else if ($id == 134) {  // Ultra(Gator) Board Contour
            $notFixAreaPrice = false;

            $laminationId = $posted_options[559];
            $lamObj = $customOptions[559]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $colorId = $posted_options[557];
            if ($colorId == 952) {
                $matOpId = $posted_options[558];
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
                if ($matOpId == 1534) {
                    $matPrice = 4.9;
                } else if ($matOpId == 1535) {
                    $matPrice = 5.2;
                } else {
                    $matPrice = 7.7;
                }
            }

            $areaPrice = $area / 144 * ($matPrice + 1.99 + $lamPrice) - $lamPrice;

            if ($posted_options[562] == 967) $has17 = true;
            else if ($posted_options[562] == 965) $has15 = true;

        } else if ($id == 135) {  // Styrene Boards Contour
            $notFixAreaPrice = false;

            $laminationId = $posted_options[569];
            $lamObj = $customOptions[569]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $matId = $posted_options[568];
            $matObj = $customOptions[568]->getValues();
            $matPrice = $matObj[$matId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;


            $areaPrice = $area / 144 * ($matPrice + $lamPrice) - $lamPrice - $matPrice;

            if ($posted_options[572] == 982) $has17 = true;
            else if ($posted_options[572] == 981) $has15 = true;

        } else if ($id == 136) {  // PVC Sintra Board Contour
            $notFixAreaPrice = false;

            $laminationId = $posted_options[579];
            $lamObj = $customOptions[579]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $matId = $posted_options[578];

            $dieCut = 1.89;

            $colorId = $posted_options[577];
            if ($colorId == 983) {
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
                if ($matId == 985) $matPrice = 3.5;
                else if ($matId == 986) $matPrice = 3.99;
                else if ($matId == 987) {
                    $matPrice = 5.95;
                    $dieCut = 1.99;
                } else {
                    $matPrice = 9;
                    $dieCut = 1.99;
                }
            }


            $areaPrice = $area / 144 * ($matPrice + $lamPrice + $dieCut) - $lamPrice;

            if ($posted_options[582] == 998) $has17 = true;
            else if ($posted_options[582] == 996) $has15 = true;

        } else if ($id == 137) {  // Foam Boards- Contour
            $notFixAreaPrice = false;

            $laminationId = $posted_options[589];
            $lamObj = $customOptions[589]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $dieCut = 1.99;

            $colorId = $posted_options[587];
            if ($colorId == 999) $matId = $posted_options[588];
            else $matId = $posted_options[955];

            if ($matId == 1001) {
                $matPrice = 2.49;
                $dieCut = 1.89;
            } else if ($matId == 1002) $matPrice = 4;
            else if ($matId == 1003) $matPrice = 4.5;
            else if ($matId == 1556) {
                $matPrice = 3.3;
                $dieCut = 1.89;
            } else {
                $matPrice = 5.4;
            }

            $areaPrice = $area / 144 * ($matPrice + $lamPrice + $dieCut) - $lamPrice;

            if ($posted_options[592] == 1013) $has17 = true;
            else if ($posted_options[592] == 1011) $has15 = true;

        } else if ($id == 138) {  // Wall Vinyl Decals Contour
            $notFixAreaPrice = false;

            $laminationId = $posted_options[599];
            $lamObj = $customOptions[599]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $matId = $posted_options[597];
            $matObj = $customOptions[597]->getValues();
            $matPrice = $matObj[$matId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = $area / 144 * ($matPrice + $lamPrice) - $lamPrice - $matPrice;

            if ($posted_options[601] == 1023) $has2 = true;
            else if ($posted_options[601] == 1021) $has15 = true;

        } else if ($id == 139) {  // Vinyl Stickers (Window Decal) Contour
            $notFixAreaPrice = false;

            $laminationId = $posted_options[609];
            $lamObj = $customOptions[609]->getValues();
            $lamPrice = $lamObj[$laminationId]->getPrice();
            $lamPrice = is_null($lamPrice) ? 0 : $lamPrice;

            $matId = $posted_options[606];
            $matObj = $customOptions[606]->getValues();
            $matPrice = $matObj[$matId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = $area / 144 * ($matPrice + $lamPrice) - $lamPrice - $matPrice;

            if ($posted_options[610] == 1035) $has17 = true;
            else if ($posted_options[610] == 1034) $has15 = true;

        } else if ($id == 140) {  // Static Clings (Window Clings) Contour
            $notFixAreaPrice = false;

            $matId = $posted_options[539];
            $matObj = $customOptions[539]->getValues();
            $matPrice = $matObj[$matId]->getPrice();
            $matPrice = is_null($matPrice) ? 0 : $matPrice;

            $areaPrice = $area / 144 * $matPrice - $matPrice;

            if ($posted_options[542] == 938) $has17 = true;
            else if ($posted_options[542] == 937) $has15 = true;

        } else if ($id == 118) {
            /** Hardware */ // Easel Backs
            $notFixAreaPrice = false;
        } else if ($id == 119) {  // Retractable Stand (stand ONLY)
            $notFixAreaPrice = false;
        } else if ($id == 120) {  // Outdoor Stand (for Banner)
            $notFixAreaPrice = false;
        } else if ($id == 121) {  // Step and Repeat Stand
            $notFixAreaPrice = false;
        } else if ($id == 122) {  // Suction Cups
            $notFixAreaPrice = false;
        } else if ($id == 124) {  // H-Stakes
            $notFixAreaPrice = false;
        } else if ($id == 154) {  // X - Stand
            $notFixAreaPrice = false;
        }


        // Default Calc

        $final_price = $quoteItem->getProduct()->getFinalPrice(); //base-price + selected options
        $this->logger->debug('$final_price: ' . $final_price);
        if ($notFixAreaPrice) $areaPrice = $this->_calculatePrice($_product, $posted_options); // Area price
        $total = $areaPrice + $final_price;
        $this->logger->debug('$areaPrice: ' . $areaPrice);
        $this->logger->debug('$total: ' . $total);

        if ($has15) $total *= 1.5;
        else if ($has17) $total *= 1.7;
        else if ($has175) $total *= 1.75;
        else if ($has2) $total *= 2;
        else if ($has34) $total *= 3.4;

        // Update Price
        $item = ($quoteItem->getParentItem() ? $quoteItem->getParentItem() : $quoteItem);

        // Check min price is 8$
        $qty = $item->getQty();

        if (($qty * $total) < 8) $total = $this->checkEightUsd($qty);

        $item->setCustomPrice($total);
        $item->setOriginalCustomPrice($total);
        $item->getProduct()->setIsSuperMode(true);

        $this->logger->debug('$total final: ' . $total);
        $this->logger->debug("================== End debug kong ====================");
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


    protected function _calculatePrice($_product, $posted_options)
    {
        $unitPrice = $_product->getPriceUnitArea();
        $area = $this->_calculateArea($_product, $posted_options);

        $discount = $this->_calculateDiscount($area, $unitPrice, $_product);

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
        $unitCoversion = $this->_pcHelper->unitConversion($inputUnit, $outputUnit);

        $area = $area * (float)$unitCoversion;

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
