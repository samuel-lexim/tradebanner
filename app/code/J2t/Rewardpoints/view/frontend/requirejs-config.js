/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    "shim": {
        'J2t_Rewardpoints/js/jquery-ui-slider-pips': ['jquery','jquery/ui']
    },
    "map": {
        '*': {
            discountPoint: 'J2t_Rewardpoints/js/discount-points',
            configurablePoints: 'J2t_Rewardpoints/js/configurablePoints',
            point_bundle: 'J2t_Rewardpoints/js/bundle',
            jQuerySliderPips: 'J2t_Rewardpoints/js/jquery-ui-slider-pips',
            "rewardpointsColorswatch": "J2t_Rewardpoints/js/j2t-rewardpoints-colorswatch"
            //swatchRenderer: 'J2t_Rewardpoints/js/j2t-rewardpoints-colorswatch'
        }
    }/*,
    config: {
        mixins: {
            'Magento_Swatches/js/SwatchRenderer': {
                'J2t_Rewardpoints/js/j2t-rewardpoints-colorswatch': true
            }
        }
    }*/
};
